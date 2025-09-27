<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingsTable extends Component
{
    use WithPagination;

    public $sortBy = 'check_in';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $search = '';
    public $statusFilter = '';
    public $showEditModal = false;
    public ?Booking $selectedBooking = null;
    public $calendarOffset = 0; // Track calendar navigation offset

    // Form fields for editing
    public $editStatus = '';
    public $editNotes = '';
    public $editPayment = "0";

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'check_in'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function editBooking($bookingId)
    {
        $this->selectedBooking = Booking::find($bookingId);
        $this->editStatus = $this->selectedBooking->status;
        $this->editNotes = $this->selectedBooking->notes ?? '';
        $this->editPayment = $this->selectedBooking->is_paid ? "1" : "0";
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedBooking = null;
        $this->editStatus = '';
        $this->editNotes = '';
        $this->editPayment = "0";
    }

    public function deleteBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->delete();
            session()->flash('success', 'Booking deleted successfully.');
        }
    }

    public function saveBooking()
    {
        if (!$this->selectedBooking) {
            return;
        }

        $validated = $this->validate([
            'editStatus' => 'required|in:pending,confirmed,cancelled',
            'editNotes' => 'nullable|string|max:2000',
            'editPayment' => 'required|in:0,1',
        ]);

        // Get fresh model from database and update it
        $booking = Booking::find($this->selectedBooking->id);
        $booking->update([
            'status' => $validated['editStatus'],
            'notes' => $validated['editNotes'],
            'is_paid' => $validated['editPayment'] === "1"
        ]);

        $this->showEditModal = false;
        $this->selectedBooking = null;
        $this->editStatus = '';
        $this->editNotes = '';
        $this->editPayment = "0";
        session()->flash('success', 'Booking updated successfully.');
    }

    public function navigateCalendar($direction)
    {
        if ($direction === 'next') {
            $this->calendarOffset += 14;
        } elseif ($direction === 'prev') {
            $this->calendarOffset -= 14;
        }
    }

    public function getCalendarData()
    {
        $days = collect();
        $startDate = now()->addDays($this->calendarOffset);

        // Get external bookings once for all dates
        $externalBookings = $this->getExternalBookings();

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Get database bookings for this date
            $dayDbBookings = Booking::with('venue')->where(function ($query) use ($date) {
                $query->where('check_in', '<=', $date->format('Y-m-d'))
                      ->where('check_out', '>', $date->format('Y-m-d'));
            })
            ->where('status', '!=', 'cancelled')
            ->get();

            // Get external bookings for this date
            $dayExternalBookings = $externalBookings->filter(function ($booking) use ($date) {
                return $booking->check_in <= $date && $booking->check_out > $date;
            });

            // Combine both types of bookings
            $dayBookings = $dayDbBookings->merge($dayExternalBookings);

            // Get check-ins for this specific date (database)
            $checkInsDb = Booking::with('venue')
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-ins for this specific date (external)
            $checkInsExternal = $externalBookings->filter(function ($booking) use ($date) {
                return $booking->check_in->format('Y-m-d') === $date->format('Y-m-d');
            });

            $checkIns = $checkInsDb->merge($checkInsExternal);

            // Get check-outs for this specific date (database)
            $checkOutsDb = Booking::with('venue')
                ->whereDate('check_out', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-outs for this specific date (external)
            $checkOutsExternal = $externalBookings->filter(function ($booking) use ($date) {
                return $booking->check_out->format('Y-m-d') === $date->format('Y-m-d');
            });

            $checkOuts = $checkOutsDb->merge($checkOutsExternal);

            $days->push([
                'date' => $date,
                'bookings' => $dayBookings,
                'booking_count' => $dayBookings->count(),
                'check_ins' => $checkIns,
                'check_in_count' => $checkIns->count(),
                'check_outs' => $checkOuts,
                'check_out_count' => $checkOuts->count(),
                'is_today' => $date->isToday(),
                'is_weekend' => $date->isWeekend()
            ]);
        }

        return $days;
    }

    private function getExternalBookings()
    {
        $externalBookings = collect();

        try {
            // Get all active iCal feeds
            $icalFeeds = \App\Models\Ical::where('active', true)->with('venue')->get();

            foreach ($icalFeeds as $feed) {
                $icalData = $this->fetchIcalData($feed->url);
                if ($icalData) {
                    $events = $this->parseIcalEvents($icalData, $feed->venue);
                    $externalBookings = $externalBookings->merge($events);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch external bookings: ' . $e->getMessage());
        }

        return $externalBookings;
    }

    private function fetchIcalData($url)
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Eileen BnB Calendar Sync/1.0'
                ]
            ]);

            return file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseIcalEvents($icalData, $venue)
    {
        $events = collect();
        $lines = explode("\r\n", str_replace(["\r\n", "\r", "\n"], "\r\n", $icalData));

        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                if (isset($currentEvent['start_date']) && isset($currentEvent['end_date'])) {
                    // Create a booking-like object for display using Booking model
                    $booking = new Booking([
                        'venue_id' => $venue->id,
                        'booking_id' => 'EXT-' . strtoupper(substr(md5($currentEvent['uid'] ?? uniqid()), 0, 6)),
                        'name' => 'External Booking',
                        'email' => 'external@booking.com',
                        'phone' => '',
                        'check_in' => $currentEvent['start_date'],
                        'check_out' => $currentEvent['end_date'],
                        'total_price' => 0,
                        'status' => 'confirmed',
                        'notes' => 'External: ' . ($currentEvent['summary'] ?? ''),
                        'nights' => $currentEvent['start_date']->diffInDays($currentEvent['end_date']),
                        'pay_method' => 'external',
                        'is_paid' => true
                    ]);

                    // Set a fake ID and mark as external
                    $booking->id = 'ext-' . uniqid();
                    $booking->venue = $venue;
                    $booking->setAttribute('is_external', true);
                    $booking->exists = false; // Don't try to save this to database

                    $events->push($booking);
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null && strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);

                if (strpos($key, 'DTSTART') === 0) {
                    $currentEvent['start_date'] = $this->parseIcalDate($value);
                } elseif (strpos($key, 'DTEND') === 0) {
                    $currentEvent['end_date'] = $this->parseIcalDate($value);
                } elseif ($key === 'UID') {
                    $currentEvent['uid'] = $value;
                } elseif ($key === 'SUMMARY') {
                    $currentEvent['summary'] = $value;
                }
            }
        }

        return $events;
    }

    private function parseIcalDate($dateString)
    {
        if (strlen($dateString) === 8) {
            return \Carbon\Carbon::createFromFormat('Ymd', $dateString);
        } elseif (strlen($dateString) === 15 && substr($dateString, -1) === 'Z') {
            return \Carbon\Carbon::createFromFormat('Ymd\THis\Z', $dateString);
        } elseif (strlen($dateString) === 15) {
            return \Carbon\Carbon::createFromFormat('Ymd\THis', $dateString);
        }

        return \Carbon\Carbon::parse($dateString);
    }

    public function render()
    {
        // Get database bookings only (for the table)
        $paginatedBookings = Booking::with('venue')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('venue', function($query) {
                          $query->where('venue_name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhere('booking_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $calendarData = $this->getCalendarData();

        return view('livewire.bookings-table', [
            'bookings' => $paginatedBookings,
            'calendarData' => $calendarData
        ]);
    }
}
