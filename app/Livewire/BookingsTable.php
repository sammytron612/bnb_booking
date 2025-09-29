<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\BookingServices\ExternalCalendarService;

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
                $checkIn = \Carbon\Carbon::parse($booking->check_in);
                $checkOut = \Carbon\Carbon::parse($booking->check_out);
                $matches = $checkIn->lte($date) && $checkOut->gt($date);



                return $matches;
            });



            // Combine both types of bookings - convert external to array to avoid collection merge issues
            $dayBookings = collect($dayDbBookings);
            foreach ($dayExternalBookings as $extBooking) {
                $dayBookings->push($extBooking);
            }



            // Get check-ins for this specific date (database)
            $checkInsDb = Booking::with('venue')
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-ins for this specific date (external)
            $checkInsExternal = $externalBookings->filter(function ($booking) use ($date) {
                return $booking->check_in->format('Y-m-d') === $date->format('Y-m-d');
            });

            // Combine check-ins manually to avoid ID collision issues
            $checkIns = collect($checkInsDb);
            foreach ($checkInsExternal as $extCheckIn) {
                $checkIns->push($extCheckIn);
            }

            // Get check-outs for this specific date (database)
            $checkOutsDb = Booking::with('venue')
                ->whereDate('check_out', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-outs for this specific date (external)
            $checkOutsExternal = $externalBookings->filter(function ($booking) use ($date) {
                return $booking->check_out->format('Y-m-d') === $date->format('Y-m-d');
            });

            // Combine check-outs manually to avoid ID collision issues
            $checkOuts = collect($checkOutsDb);
            foreach ($checkOutsExternal as $extCheckOut) {
                $checkOuts->push($extCheckOut);
            }

            // Check for double bookings (multiple bookings at same venue on same date)
            $venueBookings = [];

            // Group bookings by venue_id manually for better control
            foreach ($dayBookings as $booking) {
                $venueId = null;

                // Get venue_id from different booking types
                if (isset($booking->venue_id)) {
                    $venueId = $booking->venue_id;
                } elseif ($booking->getAttribute('venue_id')) {
                    $venueId = $booking->getAttribute('venue_id');
                } elseif (isset($booking->attributes['venue_id'])) {
                    $venueId = $booking->attributes['venue_id'];
                }

                if ($venueId) {
                    if (!isset($venueBookings[$venueId])) {
                        $venueBookings[$venueId] = [];
                    }
                    $venueBookings[$venueId][] = $booking;
                }
            }

            $hasDoubleBooking = false;
            $doubleBookingVenues = [];

            foreach ($venueBookings as $venueId => $bookings) {
                if (count($bookings) > 1) {
                    $hasDoubleBooking = true;
                    $venue = $bookings[0]->venue ?? null;
                    $doubleBookingVenues[] = [
                        'venue_id' => $venueId,
                        'venue_name' => $venue ? $venue->venue_name : 'Unknown Venue',
                        'booking_count' => count($bookings)
                    ];
                }
            }

            $days->push([
                'date' => $date,
                'bookings' => $dayBookings,
                'booking_count' => $dayBookings->count(),
                'check_ins' => $checkIns,
                'check_in_count' => $checkIns->count(),
                'check_outs' => $checkOuts,
                'check_out_count' => $checkOuts->count(),
                'is_today' => $date->isToday(),
                'is_weekend' => $date->isWeekend(),
                'has_double_booking' => $hasDoubleBooking,
                'double_booking_venues' => $doubleBookingVenues
            ]);
        }

        return $days;
    }

    private function getExternalBookings()
    {
        $externalBookings = collect();

        try {
            // Use the ExternalCalendarService directly
            $externalCalendarService = app(ExternalCalendarService::class);
            $rawExternalBookings = $externalCalendarService->getExternalBookings();



            // Convert the raw objects into Booking-like models for calendar compatibility
            if ($rawExternalBookings && $rawExternalBookings->count() > 0) {
                $index = 0;
                foreach ($rawExternalBookings as $rawBooking) {
                $sourceName = $rawBooking->source ?? 'External Booking';
                $uniqueId = 'ext-' . $rawBooking->source . '-' . $index . '-' . uniqid();


                $booking = new Booking([
                    'venue_id' => $rawBooking->venue_id,
                    'booking_id' => 'EXT-' . strtoupper(substr(md5($uniqueId), 0, 6)),
                    'name' => $sourceName,
                    'email' => 'external@booking.com',
                    'phone' => '',
                    'check_in' => $rawBooking->check_in,
                    'check_out' => $rawBooking->check_out,
                    'total_price' => 0,
                    'status' => 'confirmed',
                    'notes' => 'External calendar booking',
                    'nights' => $rawBooking->check_in->diffInDays($rawBooking->check_out),
                    'pay_method' => 'external',
                    'is_paid' => true
                ]);

                // Set venue relationship and external flag
                $booking->venue = \App\Models\Venue::find($rawBooking->venue_id);
                $booking->setAttribute('is_external', true);
                $booking->setAttribute('external_unique_id', $uniqueId);
                $booking->exists = false; // Don't try to save this to database

                // Override the getKey method to return unique ID for collection merging
                $booking->setKeyName('external_unique_id');

                $externalBookings->push($booking);
                $index++;
                }
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to fetch external bookings: ' . $e->getMessage());
        }


        return $externalBookings;
    }    public function render()
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
