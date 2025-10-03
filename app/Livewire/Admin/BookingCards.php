<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use App\Services\BookingServices\ExternalCalendarService;

class BookingCards extends Component
{
    public $calendarOffset = 0; // Track calendar navigation offset

    // Venue filtering properties
    public $selectedVenueId = null; // null = "All Venues"
    public $availableVenues = [];

    public function mount()
    {
        $this->availableVenues = \App\Models\Venue::orderBy('venue_name')->get();
    }

    public function navigateCalendar($direction)
    {
        if ($direction === 'next') {
            $this->calendarOffset += 14;
        } elseif ($direction === 'prev') {
            $this->calendarOffset -= 14;
        }
    }

    public function selectVenue($venueId)
    {
        $this->selectedVenueId = $venueId; // null for "All Venues"
        // Reset calendar offset when changing venues
        $this->calendarOffset = 0;
    }

    public function getCalendarData()
    {
        $days = collect();
        $startDate = now()->addDays($this->calendarOffset);

        // Get external bookings once for all dates
        $externalBookings = $this->getExternalBookings();

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Get database bookings for this date (with venue filtering)
            $dayDbBookings = Booking::with('venue')->where(function ($query) use ($date) {
                $query->where('check_in', '<=', $date->format('Y-m-d'))
                      ->where('check_out', '>', $date->format('Y-m-d'));
            })
            ->whereIn('status', ['confirmed', 'pending', 'refunded', 'partial_refund'])
            ->when($this->selectedVenueId, function ($query) {
                $query->where('venue_id', $this->selectedVenueId);
            })
            ->get();

            // Get external bookings for this date (with venue filtering)
            $dayExternalBookings = $externalBookings->filter(function ($booking) use ($date) {
                $checkIn = \Carbon\Carbon::parse($booking->check_in);
                $checkOut = \Carbon\Carbon::parse($booking->check_out);
                $matches = $checkIn->lte($date) && $checkOut->gt($date);

                // Apply venue filtering
                if ($this->selectedVenueId && $booking->venue_id != $this->selectedVenueId) {
                    return false;
                }

                return $matches;
            });

            // Combine both types of bookings - convert external to array to avoid collection merge issues
            $dayBookings = collect($dayDbBookings);
            foreach ($dayExternalBookings as $extBooking) {
                $dayBookings->push($extBooking);
            }

            // Get check-ins for this specific date (database with venue filtering)
            $checkInsDb = Booking::with('venue')
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->whereIn('status', ['confirmed', 'pending', 'refunded', 'partial_refund'])
                ->when($this->selectedVenueId, function ($query) {
                    $query->where('venue_id', $this->selectedVenueId);
                })
                ->get();

            // Get check-ins for this specific date (external with venue filtering)
            $checkInsExternal = $externalBookings->filter(function ($booking) use ($date) {
                $dateMatch = $booking->check_in->format('Y-m-d') === $date->format('Y-m-d');

                // Apply venue filtering
                if ($this->selectedVenueId && $booking->venue_id != $this->selectedVenueId) {
                    return false;
                }

                return $dateMatch;
            });

            // Combine check-ins manually to avoid ID collision issues
            $checkIns = collect($checkInsDb);
            foreach ($checkInsExternal as $extCheckIn) {
                $checkIns->push($extCheckIn);
            }

            // Get check-outs for this specific date (database with venue filtering)
            $checkOutsDb = Booking::with('venue')
                ->whereDate('check_out', $date->format('Y-m-d'))
                ->whereIn('status', ['confirmed', 'pending', 'refunded', 'partial_refund'])
                ->when($this->selectedVenueId, function ($query) {
                    $query->where('venue_id', $this->selectedVenueId);
                })
                ->get();

            // Get check-outs for this specific date (external with venue filtering)
            $checkOutsExternal = $externalBookings->filter(function ($booking) use ($date) {
                $dateMatch = $booking->check_out->format('Y-m-d') === $date->format('Y-m-d');

                // Apply venue filtering
                if ($this->selectedVenueId && $booking->venue_id != $this->selectedVenueId) {
                    return false;
                }

                return $dateMatch;
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
    }

    public function render()
    {
        $calendarData = $this->getCalendarData();

        return view('livewire.admin.booking-cards', [
            'calendarData' => $calendarData
        ]);
    }
}
