<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\-\.\']+$/u',
            'email' => 'required|email:rfc,dns|max:255',
            'phone' => 'required|string|min:10|max:20|regex:/^[\+]?[0-9\s\-\(\)\.]+$/',
            'depart' => 'required|date|after_or_equal:today',
            'leave' => 'required|date|after:depart',
            'venue_id' => 'required|integer|exists:venues,id',
            'total_price' => 'required|numeric|min:0',
        ], [
            'name.regex' => 'Name can only contain letters, spaces, hyphens, periods, and apostrophes.',
            'email.email' => 'Please enter a valid email address with a valid domain.',
            'phone.regex' => 'Please enter a valid phone number (numbers, spaces, hyphens, parentheses, and + allowed).',
            'phone.min' => 'Phone number must be at least 10 characters.',
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

        // SECURITY: Server-side price validation to prevent manipulation
        $venue = \App\Models\Venue::findOrFail($request->venue_id);
        $calculatedPrice = $nights * $venue->price;

        // Allow small floating point differences (within 1 penny)
        if (abs($request->total_price - $calculatedPrice) > 0.01) {
            \Log::warning('Price manipulation attempt detected', [
                'submitted_price' => $request->total_price,
                'calculated_price' => $calculatedPrice,
                'venue_id' => $request->venue_id,
                'nights' => $nights,
                'venue_price' => $venue->price,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid price calculation. Please refresh and try again.'
            ], 422);
        }

        try {
            // SECURITY: Use database transaction with locking to prevent race conditions
            $booking = \DB::transaction(function () use ($request, $nights) {
                // Lock venue to prevent concurrent bookings
                $venue = \App\Models\Venue::where('id', $request->venue_id)->lockForUpdate()->first();

                if (!$venue) {
                    throw new \Exception('Venue not found');
                }

                // Check for booking conflicts within the transaction
                $hasConflict = Booking::where('venue_id', $request->venue_id)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) use ($request) {
                        $query->where(function ($q) use ($request) {
                            // New booking starts during existing booking
                            $q->where('check_in', '<=', $request->depart)
                              ->where('check_out', '>', $request->depart);
                        })->orWhere(function ($q) use ($request) {
                            // New booking ends during existing booking
                            $q->where('check_in', '<', $request->leave)
                              ->where('check_out', '>=', $request->leave);
                        })->orWhere(function ($q) use ($request) {
                            // New booking completely contains existing booking
                            $q->where('check_in', '>=', $request->depart)
                              ->where('check_out', '<=', $request->leave);
                        })->orWhere(function ($q) use ($request) {
                            // Existing booking completely contains new booking
                            $q->where('check_in', '<=', $request->depart)
                              ->where('check_out', '>=', $request->leave);
                        });
                    })->exists();

                if ($hasConflict) {
                    throw new \Exception('These dates are no longer available. Please select different dates.');
                }

                // Create booking only if no conflicts
                return Booking::create([
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
            }, 3); // Retry 3 times on deadlock

            return response()->json([
                'success' => true,
                'message' => 'Booking request submitted successfully!',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            \Log::warning('Booking creation failed in controller', [
                'venue_id' => $request->venue_id,
                'check_in' => $request->depart,
                'check_out' => $request->leave,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Failed to create booking. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all bookings for a specific venue
     */
    public function getBookingsForVenue($venue_id)
    {
        $bookings = Booking::where('venue_id', $venue_id)
            ->with('venue')
            ->orderBy('check_in', 'asc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get upcoming bookings (for calendar blocking)
     */
    public function getUpcomingBookings()
    {
        $bookings = Booking::where('status', '!=', 'cancelled')
            ->where('check_out', '>=', Carbon::today())
            ->with('venue')
            ->select('check_in', 'check_out', 'venue_id')
            ->get();

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

        $checkInDates = [];      // Dates when guests check in (bookings start)
        $checkOutDates = [];     // Dates when guests check out (bookings end)
        $fullyBookedDates = [];  // Dates that are completely unavailable
        $bookedDates = [];       // All booked dates (for backward compatibility)

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

        // Remove duplicates and sort all arrays
        $checkInDates = array_unique($checkInDates);
        $checkOutDates = array_unique($checkOutDates);
        $fullyBookedDates = array_unique($fullyBookedDates);
        $bookedDates = array_unique($bookedDates);

        sort($checkInDates);
        sort($checkOutDates);
        sort($fullyBookedDates);
        sort($bookedDates);

        return response()->json([
            'success' => true,
            'checkInDates' => $checkInDates,
            'checkOutDates' => $checkOutDates,
            'fullyBookedDates' => $fullyBookedDates,
            'bookedDates' => $bookedDates, // For backward compatibility
            'count' => count($fullyBookedDates)
        ]);
    }
}
