<?php

namespace App\Services\BookingServices;

use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingValidationService
{
    protected $externalCalendarService;

    public function __construct(ExternalCalendarService $externalCalendarService)
    {
        $this->externalCalendarService = $externalCalendarService;
    }

    public function validateBookingDates(string $checkIn, string $checkOut, int $venueId, ?int $excludeBookingId = null): array
    {
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $errors = [];

        // Basic date validation
        if ($checkInDate->isPast()) {
            $errors[] = 'Check-in date cannot be in the past.';
        }

        if ($checkOutDate->lte($checkInDate)) {
            $errors[] = 'Check-out date must be after check-in date.';
        }

        // Check for conflicts with existing database bookings
        $conflictingBookings = $this->getConflictingDatabaseBookings($checkInDate, $checkOutDate, $venueId, $excludeBookingId);
        if ($conflictingBookings->isNotEmpty()) {
            $errors[] = 'These dates conflict with existing bookings.';
        }

        // Check for conflicts with external calendar bookings
        $externalConflicts = $this->getConflictingExternalBookings($checkInDate, $checkOutDate, $venueId);
        if ($externalConflicts->isNotEmpty()) {
            $errors[] = 'These dates conflict with external calendar bookings.';
        }

        // Check for orphaned dates (single nights that can't accommodate minimum stay)
        $orphanedDateErrors = $this->checkForOrphanedDates($checkInDate, $checkOutDate, $venueId);
        if (!empty($orphanedDateErrors)) {
            $errors = array_merge($errors, $orphanedDateErrors);
        }

        return $errors;
    }

    public function getConflictingDatabaseBookings(Carbon $checkIn, Carbon $checkOut, int $venueId, ?int $excludeBookingId = null): Collection
    {
        return Booking::where('venue_id', $venueId)
            ->whereIn('status', ['confirmed', 'pending', 'refunded', 'partial_refund'])
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                $query->where('id', '!=', $excludeBookingId);
            })
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // New booking starts during existing booking
                    $q->where('check_in', '<=', $checkIn->format('Y-m-d'))
                      ->where('check_out', '>', $checkIn->format('Y-m-d'));
                })->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // New booking ends during existing booking
                    $q->where('check_in', '<', $checkOut->format('Y-m-d'))
                      ->where('check_out', '>', $checkOut->format('Y-m-d'));
                })->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // New booking completely contains existing booking
                    $q->where('check_in', '>=', $checkIn->format('Y-m-d'))
                      ->where('check_out', '<=', $checkOut->format('Y-m-d'));
                });
            })
            ->get();
    }

    public function getConflictingExternalBookings(Carbon $checkIn, Carbon $checkOut, int $venueId): Collection
    {
        $externalBookings = $this->externalCalendarService->getExternalBookings();

        return $externalBookings->filter(function ($booking) use ($checkIn, $checkOut, $venueId) {
            if ($booking->venue_id != $venueId) {
                return false;
            }

            $extCheckIn = Carbon::parse($booking->check_in);
            $extCheckOut = Carbon::parse($booking->check_out);

            return ($checkIn->lt($extCheckOut) && $checkOut->gt($extCheckIn));
        });
    }

    public function isDateAvailable(Carbon $date, int $venueId): bool
    {
        // Check database bookings
        $dbBooking = Booking::where('venue_id', $venueId)
            ->whereIn('status', ['confirmed', 'pending', 'refunded', 'partial_refund'])
            ->where('check_in', '<=', $date->format('Y-m-d'))
            ->where('check_out', '>', $date->format('Y-m-d'))
            ->exists();

        if ($dbBooking) {
            return false;
        }

        // Check external bookings
        $externalBookings = $this->externalCalendarService->getExternalBookings();
        foreach ($externalBookings as $booking) {
            if ($booking->venue_id != $venueId) {
                continue;
            }

            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);

            if ($checkIn->lte($date) && $checkOut->gt($date)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check for orphaned dates that would be created by this booking
     * An orphaned date is a single available night that can't accommodate minimum stay
     */
    public function checkForOrphanedDates(Carbon $checkIn, Carbon $checkOut, int $venueId): array
    {
        $errors = [];
        $minNights = 2; // Minimum stay requirement

        // Check for orphaned dates that would be created before the check-in
        $beforeCheckIn = $checkIn->copy()->subDay();
        if ($this->wouldCreateOrphanedDate($beforeCheckIn, $venueId, $minNights)) {
            $errors[] = "Your check-in date would create an unbookable single night on " . $beforeCheckIn->format('j/n/Y') . ". Please adjust your dates.";
        }

        // Check for orphaned dates that would be created after the check-out
        $afterCheckOut = $checkOut->copy();
        if ($this->wouldCreateOrphanedDate($afterCheckOut, $venueId, $minNights)) {
            $errors[] = "Your check-out date would create an unbookable single night on " . $afterCheckOut->format('j/n/Y') . ". Please adjust your dates.";
        }

        return $errors;
    }

    /**
     * Check if a specific date would be orphaned (single night surrounded by bookings)
     */
    private function wouldCreateOrphanedDate(Carbon $date, int $venueId, int $minNights): bool
    {
        // If the date itself is already booked, it can't be orphaned
        if (!$this->isDateAvailable($date, $venueId)) {
            return false;
        }

        // Check if there are enough consecutive available nights to accommodate minimum stay
        // starting from this date
        $canStartBooking = true;
        for ($i = 0; $i < $minNights; $i++) {
            $checkDate = $date->copy()->addDays($i);
            if (!$this->isDateAvailable($checkDate, $venueId)) {
                $canStartBooking = false;
                break;
            }
        }

        // Check if there are enough consecutive available nights to accommodate minimum stay
        // ending on this date
        $canEndBooking = true;
        for ($i = 1; $i <= $minNights; $i++) {
            $checkDate = $date->copy()->subDays($i);
            if (!$this->isDateAvailable($checkDate, $venueId)) {
                $canEndBooking = false;
                break;
            }
        }

        // Date is orphaned if you can neither start nor end a minimum stay booking there
        return !$canStartBooking && !$canEndBooking;
    }
}
