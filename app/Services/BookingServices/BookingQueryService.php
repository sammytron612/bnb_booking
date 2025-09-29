<?php

namespace App\Services\BookingServices;

use App\Models\Booking;
use Carbon\Carbon;

class BookingQueryService
{
    protected $externalCalendarService;

    public function __construct(ExternalCalendarService $externalCalendarService)
    {
        $this->externalCalendarService = $externalCalendarService;
    }

    /**
     * Get all booked dates data for calendar display
     *
     * @param int|null $venueId Optional venue filter
     * @return array
     */
    public function getBookedDatesData($venueId = null)
    {
        // Get confirmed and pending bookings (exclude cancelled)
        $bookingsQuery = Booking::where('status', '!=', 'cancelled')
            ->where('check_out', '>=', Carbon::today()) // Only future/current bookings
            ->with('venue')
            ->select('check_in', 'check_out', 'venue_id');

        // Filter by venue if specified
        if ($venueId) {
            $bookingsQuery->where('venue_id', $venueId);
        }

        $bookings = $bookingsQuery->get();

        // Get external bookings from iCal feeds
        $externalBookings = $this->externalCalendarService->getExternalBookings($venueId);

        $checkInDates = [];      // Dates when guests check in (bookings start)
        $checkOutDates = [];     // Dates when guests check out (bookings end)
        $fullyBookedDates = [];  // Dates that are completely unavailable
        $bookedDates = [];       // All booked dates (for backward compatibility)

        // Process database bookings
        foreach ($bookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);

            // Add check-in date (guests arrive this day at 3pm)
            $checkInDates[] = $checkInDate->format('Y-m-d');

            // Add check-out date (guests leave this day at 11am)
            $checkOutDates[] = $checkOutDate->format('Y-m-d');

            // Add all nights when property is occupied
            // From check-in date up to (but not including) check-out date
            // This represents the nights guests are staying
            $current = $checkInDate->copy();
            while ($current < $checkOutDate) {
                $dateStr = $current->format('Y-m-d');
                $fullyBookedDates[] = $dateStr;
                $bookedDates[] = $dateStr; // For backward compatibility
                $current->addDay();
            }
        }

        // Process external bookings
        foreach ($externalBookings as $booking) {
            // Skip past bookings
            if ($booking->check_out < Carbon::today()) {
                continue;
            }

            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);

            // Add check-in date
            $checkInDates[] = $checkInDate->format('Y-m-d');

            // Add check-out date
            $checkOutDates[] = $checkOutDate->format('Y-m-d');

            // Add all nights when property is occupied
            $current = $checkInDate->copy();
            while ($current < $checkOutDate) {
                $dateStr = $current->format('Y-m-d');
                $fullyBookedDates[] = $dateStr;
                $bookedDates[] = $dateStr; // For backward compatibility
                $current->addDay();
            }
        }

        // Remove duplicates and sort all arrays
        $checkInDates = array_unique($checkInDates);
        $checkOutDates = array_unique($checkOutDates);
        $fullyBookedDates = array_unique($fullyBookedDates);
        $bookedDates = array_unique($bookedDates);

        sort($checkInDates);
        sort($checkOutDates);
        sort($fullyBookedDates);
        sort($bookedDates);

        return [
            'success' => true,
            'checkInDates' => $checkInDates,
            'checkOutDates' => $checkOutDates,
            'fullyBookedDates' => $fullyBookedDates,
            'bookedDates' => $bookedDates, // For backward compatibility
            'count' => count($fullyBookedDates)
        ];
    }

    /**
     * Get all bookings for a specific venue
     *
     * @param int $venueId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsForVenue($venueId)
    {
        return Booking::where('venue_id', $venueId)
            ->with('venue')
            ->orderBy('check_in', 'asc')
            ->get();
    }

    /**
     * Get upcoming bookings (for calendar blocking)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingBookings()
    {
        return Booking::where('status', '!=', 'cancelled')
            ->where('check_out', '>=', Carbon::today())
            ->with('venue')
            ->select('check_in', 'check_out', 'venue_id')
            ->get();
    }
}
