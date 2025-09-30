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

        // Check same-day turnover policy
        $sameDayIssues = $this->checkSameDayTurnover($checkInDate, $checkOutDate, $venueId, $excludeBookingId);
        if (!empty($sameDayIssues)) {
            $errors = array_merge($errors, $sameDayIssues);
        }

        return $errors;
    }

    public function getConflictingDatabaseBookings(Carbon $checkIn, Carbon $checkOut, int $venueId, ?int $excludeBookingId = null): Collection
    {
        return Booking::where('venue_id', $venueId)
            ->whereIn('status', ['confirmed', 'pending'])
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
                      ->where('check_out', '>=', $checkOut->format('Y-m-d'));
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

    public function checkSameDayTurnover(Carbon $checkIn, Carbon $checkOut, int $venueId, ?int $excludeBookingId = null): array
    {
        $errors = [];

        // SAME-DAY TURNOVER IS ALLOWED IN HOSPITALITY INDUSTRY
        // One guest checks out at 11 AM, next guest checks in at 3 PM
        // This is standard practice and should not be blocked

        // Only check for overlapping dates, not same-day turnover
        // Same-day turnover is actually desired for maximum occupancy

        return $errors;
    }

    public function isDateAvailable(Carbon $date, int $venueId): bool
    {
        // Check database bookings
        $dbBooking = Booking::where('venue_id', $venueId)
            ->whereIn('status', ['confirmed', 'pending'])
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
}
