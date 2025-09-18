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
            'venue' => 'required|string|in:The Light House,Saras',
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

        try {
            $booking = Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'check_in' => $request->depart,
                'check_out' => $request->leave,
                'venue' => $request->venue,
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
    public function getBookingsForVenue($venue)
    {
        $bookings = Booking::forVenue($venue)
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
            ->select('check_in', 'check_out', 'venue')
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
        $venue = $request->query('venue'); // Optional venue filter

        // Get confirmed and pending bookings (exclude cancelled)
        $bookingsQuery = Booking::where('status', '!=', 'cancelled')
            ->where('check_out', '>=', Carbon::today()) // Only future/current bookings
            ->select('check_in', 'check_out', 'venue');

        // Filter by venue if specified
        if ($venue) {
            $bookingsQuery->where('venue', $venue);
        }

        $bookings = $bookingsQuery->get();

        $bookedDates = [];

        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->depart);
            $end = Carbon::parse($booking->leave);

            // Add each date from check-in to check-out (exclusive of check-out date)
            while ($start < $end) {
                $bookedDates[] = $start->format('Y-m-d');
                $start->addDay();
            }
        }

        // Remove duplicates and sort
        $bookedDates = array_unique($bookedDates);
        sort($bookedDates);

        return response()->json([
            'success' => true,
            'bookedDates' => $bookedDates,
            'count' => count($bookedDates)
        ]);
    }
}
