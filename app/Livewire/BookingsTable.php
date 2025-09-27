<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Ical;
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
    public $successMessage = ''; // Livewire success message

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
            $this->successMessage = 'Booking deleted successfully.';
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
        $this->successMessage = 'Booking updated successfully.';
    }

    public function clearSuccessMessage()
    {
        $this->successMessage = '';
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

        // Get iCal blocked dates for the date range
        $endDate = $startDate->copy()->addDays(13);
        $icalBlockedDates = $this->getIcalBlockedDates($startDate, $endDate);

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');

            // Get bookings for this date
            $dayBookings = Booking::with('venue')->where(function ($query) use ($date) {
                $query->where('check_in', '<=', $date->format('Y-m-d'))
                      ->where('check_out', '>', $date->format('Y-m-d'));
            })
            ->where('status', '!=', 'cancelled')
            ->get();

            // Get check-ins for this specific date
            $checkIns = Booking::with('venue')
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-outs for this specific date
            $checkOuts = Booking::with('venue')
                ->whereDate('check_out', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get iCal bookings for this date
            $icalBookings = collect($icalBlockedDates)->filter(function ($icalDate) use ($dateStr) {
                return $icalDate['date'] === $dateStr;
            })->values();

            $days->push([
                'date' => $date,
                'bookings' => $dayBookings,
                'booking_count' => $dayBookings->count(),
                'check_ins' => $checkIns,
                'check_in_count' => $checkIns->count(),
                'check_outs' => $checkOuts,
                'check_out_count' => $checkOuts->count(),
                'ical_bookings' => $icalBookings,
                'ical_count' => $icalBookings->count(),
                'is_today' => $date->isToday(),
                'is_weekend' => $date->isWeekend()
            ]);
        }

        return $days;
    }

    private function getIcalBlockedDates($startDate, $endDate)
    {
        // Get all iCal URLs that are active
        $icals = \App\Models\Ical::where('active', true)->with('venue')->get();
        $blockedDates = [];

        foreach ($icals as $ical) {
            try {
                // Fetch iCal data
                $icalContent = file_get_contents($ical->url);
                if ($icalContent) {
                    $dates = $this->parseIcalContent($icalContent, $startDate, $endDate);
                    foreach ($dates as $date) {
                        $blockedDates[] = [
                            'date' => $date,
                            'source' => $ical->source,
                            'venue' => $ical->venue,
                            'name' => $ical->name
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't break the calendar
                \Log::error("Error fetching iCal for {$ical->name}: " . $e->getMessage());
            }
        }

        return $blockedDates;
    }

    private function parseIcalContent($content, $startDate, $endDate)
    {
        $dates = [];
        $lines = explode("\n", $content);
        $inEvent = false;
        $eventStart = null;
        $eventEnd = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $inEvent = true;
                $eventStart = null;
                $eventEnd = null;
            } elseif ($line === 'END:VEVENT' && $inEvent) {
                if ($eventStart && $eventEnd) {
                    // Generate all dates between start and end (exclusive of end date)
                    $current = \Carbon\Carbon::parse($eventStart);
                    $end = \Carbon\Carbon::parse($eventEnd);

                    while ($current->lt($end) && $current->between($startDate, $endDate)) {
                        $dates[] = $current->format('Y-m-d');
                        $current->addDay();
                    }
                }
                $inEvent = false;
            } elseif ($inEvent) {
                if (strpos($line, 'DTSTART') === 0) {
                    $eventStart = $this->parseIcalDate($line);
                } elseif (strpos($line, 'DTEND') === 0) {
                    $eventEnd = $this->parseIcalDate($line);
                }
            }
        }

        return array_unique($dates);
    }

    private function parseIcalDate($line)
    {
        $parts = explode(':', $line, 2);
        if (count($parts) === 2) {
            $dateStr = $parts[1];
            // Handle different date formats (with or without time)
            if (strlen($dateStr) === 8) {
                // YYYYMMDD format
                return \Carbon\Carbon::createFromFormat('Ymd', $dateStr)->format('Y-m-d');
            } elseif (strlen($dateStr) === 15) {
                // YYYYMMDDTHHMMSSZ format
                return \Carbon\Carbon::createFromFormat('Ymd\THis\Z', $dateStr)->format('Y-m-d');
            }
        }
        return null;
    }

    public function render()
    {
        $bookings = Booking::with('venue')
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
            'bookings' => $bookings,
            'calendarData' => $calendarData
        ]);
    }
}
