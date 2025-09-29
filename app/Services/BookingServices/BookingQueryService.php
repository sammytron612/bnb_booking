<?php

namespace App\Services\BookingServices;

use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class BookingQueryService
{
    protected $externalCalendarService;

    public function __construct(ExternalCalendarService $externalCalendarService)
    {
        $this->externalCalendarService = $externalCalendarService;
    }

    public function getBookingsForDateRange(Carbon $startDate, Carbon $endDate, ?int $venueId = null): Collection
    {
        // Get database bookings
        $dbBookings = $this->getDatabaseBookingsForDateRange($startDate, $endDate, $venueId);

        // Get external bookings
        $externalBookings = $this->getExternalBookingsForDateRange($startDate, $endDate, $venueId);

        // Combine and return
        return $dbBookings->merge($externalBookings);
    }

    public function getDatabaseBookingsForDateRange(Carbon $startDate, Carbon $endDate, ?int $venueId = null): Collection
    {
        $query = Booking::with('venue')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    // Booking starts within date range
                    $q->whereBetween('check_in', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    // Booking ends within date range
                    $q->whereBetween('check_out', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->orWhere(function ($q) use ($startDate, $endDate) {
                    // Booking spans entire date range
                    $q->where('check_in', '<=', $startDate->format('Y-m-d'))
                      ->where('check_out', '>=', $endDate->format('Y-m-d'));
                });
            });

        if ($venueId) {
            $query->where('venue_id', $venueId);
        }

        return $query->get();
    }

    public function getExternalBookingsForDateRange(Carbon $startDate, Carbon $endDate, ?int $venueId = null): Collection
    {
        $externalBookings = $this->externalCalendarService->getExternalBookings();

        return $externalBookings->filter(function ($booking) use ($startDate, $endDate, $venueId) {
            if ($venueId && $booking->venue_id != $venueId) {
                return false;
            }

            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);

            // Check if booking overlaps with date range
            return ($checkIn->lt($endDate) && $checkOut->gt($startDate));
        });
    }

    public function getBookingsForDate(Carbon $date, ?int $venueId = null): Collection
    {
        // Get database bookings for specific date
        $dbBookings = Booking::with('venue')
            ->where('status', '!=', 'cancelled')
            ->where('check_in', '<=', $date->format('Y-m-d'))
            ->where('check_out', '>', $date->format('Y-m-d'))
            ->when($venueId, function ($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            })
            ->get();

        // Get external bookings for specific date
        $externalBookings = $this->externalCalendarService->getExternalBookings()
            ->filter(function ($booking) use ($date, $venueId) {
                if ($venueId && $booking->venue_id != $venueId) {
                    return false;
                }

                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);

                return ($checkIn->lte($date) && $checkOut->gt($date));
            });

        return $dbBookings->merge($externalBookings);
    }

    public function getCheckInsForDate(Carbon $date, ?int $venueId = null): Collection
    {
        // Get database check-ins
        $dbCheckIns = Booking::with('venue')
            ->where('status', '!=', 'cancelled')
            ->whereDate('check_in', $date->format('Y-m-d'))
            ->when($venueId, function ($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            })
            ->get();

        // Get external check-ins
        $externalCheckIns = $this->externalCalendarService->getExternalBookings()
            ->filter(function ($booking) use ($date, $venueId) {
                if ($venueId && $booking->venue_id != $venueId) {
                    return false;
                }

                $checkIn = Carbon::parse($booking->check_in);
                return $checkIn->format('Y-m-d') === $date->format('Y-m-d');
            });

        return $dbCheckIns->merge($externalCheckIns);
    }

    public function getCheckOutsForDate(Carbon $date, ?int $venueId = null): Collection
    {
        // Get database check-outs
        $dbCheckOuts = Booking::with('venue')
            ->where('status', '!=', 'cancelled')
            ->whereDate('check_out', $date->format('Y-m-d'))
            ->when($venueId, function ($query) use ($venueId) {
                $query->where('venue_id', $venueId);
            })
            ->get();

        // Get external check-outs
        $externalCheckOuts = $this->externalCalendarService->getExternalBookings()
            ->filter(function ($booking) use ($date, $venueId) {
                if ($venueId && $booking->venue_id != $venueId) {
                    return false;
                }

                $checkOut = Carbon::parse($booking->check_out);
                return $checkOut->format('Y-m-d') === $date->format('Y-m-d');
            });

        return $dbCheckOuts->merge($externalCheckOuts);
    }

    public function getAvailableDatesInRange(Carbon $startDate, Carbon $endDate, int $venueId): Collection
    {
        $availableDates = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            if ($this->isDateAvailable($currentDate, $venueId)) {
                $availableDates->push($currentDate->copy());
            }
            $currentDate->addDay();
        }

        return $availableDates;
    }

    public function isDateAvailable(Carbon $date, int $venueId): bool
    {
        // Check database bookings
        $dbBooking = Booking::where('venue_id', $venueId)
            ->where('status', '!=', 'cancelled')
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

    public function getBookingStatistics(?int $venueId = null): array
    {
        $query = Booking::query();

        if ($venueId) {
            $query->where('venue_id', $venueId);
        }

        $totalBookings = $query->count();
        $confirmedBookings = $query->where('status', 'confirmed')->count();
        $pendingBookings = $query->where('status', 'pending')->count();
        $cancelledBookings = $query->where('status', 'cancelled')->count();
        $paidBookings = $query->where('is_paid', true)->count();

        return [
            'total' => $totalBookings,
            'confirmed' => $confirmedBookings,
            'pending' => $pendingBookings,
            'cancelled' => $cancelledBookings,
            'paid' => $paidBookings,
            'paid_percentage' => $totalBookings > 0 ? round(($paidBookings / $totalBookings) * 100, 1) : 0
        ];
    }
}
