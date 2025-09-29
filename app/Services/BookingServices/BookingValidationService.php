<?php

namespace App\Services\BookingServices;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingValidationService
{
    protected $externalCalendarService;

    public function __construct(ExternalCalendarService $externalCalendarService)
    {
        $this->externalCalendarService = $externalCalendarService;
    }

    /**
     * Real-time validation against all booking sources
     * Provides comprehensive availability check with detailed error reporting
     *
     * @param int $venueId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @return array
     */
    public function validateDatesAgainstAllSources($venueId, $checkIn, $checkOut)
    {
        try {
            // Check database bookings (allowing same-day turnover)
            $dbConflicts = Booking::where('venue_id', $venueId)
                ->where('status', '!=', 'cancelled')
                ->where('check_in', '<', $checkOut)     // Existing starts before new ends
                ->where('check_out', '>', $checkIn)     // Existing ends after new starts
                ->exists();

            if ($dbConflicts) {
                return [
                    'available' => false,
                    'details' => 'Database booking conflict detected'
                ];
            }

            // Check external bookings with fresh data
            $externalBookings = $this->externalCalendarService->getExternalBookings($venueId);

            foreach ($externalBookings as $externalBooking) {
                $extCheckIn = Carbon::parse($externalBooking->check_in);
                $extCheckOut = Carbon::parse($externalBooking->check_out);

                if (($checkIn < $extCheckOut) && ($checkOut > $extCheckIn)) {
                    return [
                        'available' => false,
                        'details' => 'External calendar conflict: ' . ($externalBooking->source ?? 'External')
                    ];
                }
            }

            return ['available' => true];

        } catch (\Exception $e) {
            Log::error('Real-time validation failed: ' . $e->getMessage());

            // Fail safe - if validation fails, assume not available
            return [
                'available' => false,
                'details' => 'Validation system error - please try again'
            ];
        }
    }

    /**
     * Validate minimum stay requirements
     *
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @param int $minimumNights
     * @return array
     */
    public function validateMinimumStay($checkIn, $checkOut, $minimumNights = 2)
    {
        $nights = $checkIn->diffInDays($checkOut);

        if ($nights < $minimumNights) {
            return [
                'valid' => false,
                'message' => "Minimum stay is {$minimumNights} nights"
            ];
        }

        return ['valid' => true];
    }

    /**
     * Check for database booking conflicts only
     *
     * @param int $venueId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @return array
     */
    public function checkDatabaseConflicts($venueId, $checkIn, $checkOut)
    {
        $conflictingBookings = Booking::where('venue_id', $venueId)
            ->where('status', '!=', 'cancelled')
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();

        return [
            'has_conflict' => $conflictingBookings,
            'message' => $conflictingBookings
                ? 'Selected dates are not available due to existing booking.'
                : 'No database conflicts found'
        ];
    }
}
