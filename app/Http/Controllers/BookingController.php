<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\BookingServices\ExternalCalendarService;
use App\Services\BookingServices\BookingValidationService;
use App\Services\BookingServices\BookingQueryService;

class BookingController extends Controller
{
    protected $externalCalendarService;
    protected $bookingValidationService;
    protected $bookingQueryService;

    public function __construct(
        ExternalCalendarService $externalCalendarService,
        BookingValidationService $bookingValidationService,
        BookingQueryService $bookingQueryService
    ) {
        $this->externalCalendarService = $externalCalendarService;
        $this->bookingValidationService = $bookingValidationService;
        $this->bookingQueryService = $bookingQueryService;
    }
    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'depart' => 'required|date|after_or_equal:today',
            'leave' => 'required|date|after:depart',
            'venue_id' => 'required|integer|exists:venues,id',
            'total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate nights
        $checkIn = Carbon::parse($request->depart);
        $checkOut = Carbon::parse($request->leave);
        $nights = $checkIn->diffInDays($checkOut);

        // Ensure minimum 2 nights
        if ($nights < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum stay is 2 nights'
            ], 422);
        }

        // Check database bookings for conflicts (allowing same-day turnover)
        $conflictingBookings = Booking::where('venue_id', $request->venue_id)
            ->where('status', '!=', 'cancelled')
            ->where('check_in', '<', $checkOut)     // Existing starts before new ends
            ->where('check_out', '>', $checkIn)     // Existing ends after new starts
            ->exists();

        if ($conflictingBookings) {
            return response()->json([
                'success' => false,
                'message' => 'Selected dates are not available due to existing booking. Please choose different dates.',
                'error_code' => 'DATABASE_BOOKING_CONFLICT'
            ], 422);
        }

        //  CRITICAL: Check external calendar bookings (Airbnb, Booking.com, etc.)
        try {
            $externalBookings = $this->externalCalendarService->getExternalBookings($request->venue_id);

            foreach ($externalBookings as $externalBooking) {
                $extCheckIn = Carbon::parse($externalBooking->check_in);
                $extCheckOut = Carbon::parse($externalBooking->check_out);

                // Check for overlap with external bookings
                if (($checkIn < $extCheckOut) && ($checkOut > $extCheckIn)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected dates are not available due to external booking (' . ($externalBooking->source ?? 'External') . '). Please choose different dates.',
                        'error_code' => 'EXTERNAL_BOOKING_CONFLICT',
                        'conflict_source' => $externalBooking->source ?? 'External'
                    ], 422);
                }
            }
        } catch (\Exception $e) {
            \Log::error('External booking validation failed: ' . $e->getMessage(), [
                'venue_id' => $request->venue_id,
                'check_in' => $request->depart,
                'check_out' => $request->leave
            ]);

            // Continue with booking if external validation fails (graceful degradation)
            // But log the issue for investigation
        }

        try {
            $booking = Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'check_in' => $request->depart,
                'check_out' => $request->leave,
                'venue_id' => $request->venue_id,
                'nights' => $nights,
                'total_price' => $request->total_price,
                'status' => 'pending',
                'notes' => $request->notes ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully!',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Booking creation failed: ' . $e->getMessage(), [
                'venue_id' => $request->venue_id,
                'check_in' => $request->depart,
                'check_out' => $request->leave,
                'user_data' => $request->only(['name', 'email'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all bookings for a specific venue
     */
    public function getBookingsForVenue($venue_id)
    {
        $bookings = $this->bookingQueryService->getBookingsForVenue($venue_id);
        return response()->json($bookings);
    }

    /**
     * Get upcoming bookings (for calendar blocking)
     */
    public function getUpcomingBookings()
    {
        $bookings = $this->bookingQueryService->getUpcomingBookings();
        return response()->json($bookings);
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'booking' => $booking
        ]);
    }

    /**
     * Get all booked dates for calendar display
     */
    public function getBookedDates(Request $request)
    {
        $venueId = $request->query('venue_id'); // Optional venue filter

        // Use the service to get booked dates data
        $data = $this->bookingQueryService->getBookedDatesData($venueId);

        $response = response()->json($data);

        // Force no caching for booking data to ensure real-time updates
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');

        return $response;
    }




}
