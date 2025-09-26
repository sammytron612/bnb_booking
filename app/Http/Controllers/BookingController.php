<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
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
