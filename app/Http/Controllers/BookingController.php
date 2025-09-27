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

        // Get external bookings from iCal feeds
        $externalBookings = $this->getExternalBookings($venueId);

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
            }            $checkInDate = Carbon::parse($booking->check_in);
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

        return response()->json([
            'success' => true,
            'checkInDates' => $checkInDates,
            'checkOutDates' => $checkOutDates,
            'fullyBookedDates' => $fullyBookedDates,
            'bookedDates' => $bookedDates, // For backward compatibility
            'count' => count($fullyBookedDates)
        ]);
    }

    /**
     * Get external bookings from iCal feeds
     */
    public function getExternalBookings($venueId = null)
    {
        $externalBookings = collect();

        try {
            // Get active iCal feeds
            $icalFeeds = \App\Models\Ical::where('active', true)->with('venue');

            if ($venueId) {
                $icalFeeds = $icalFeeds->where('venue_id', $venueId);
            }

            $icalFeeds = $icalFeeds->get();

            foreach ($icalFeeds as $feed) {
                $icalData = $this->fetchIcalData($feed->url);
                if ($icalData) {
                    $events = $this->parseIcalEvents($icalData, $feed->venue, $feed);
                    // Use push instead of merge to avoid collection key conflicts
                    foreach ($events as $event) {
                        $externalBookings->push($event);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch external bookings: ' . $e->getMessage());
        }

        return $externalBookings;
    }

    private function fetchIcalData($url)
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Eileen BnB Calendar Sync/1.0'
                ]
            ]);

            return file_get_contents($url, false, $context);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseIcalEvents($icalData, $venue, $feed = null)
    {
        $events = collect();
        $lines = explode("\r\n", str_replace(["\r\n", "\r", "\n"], "\r\n", $icalData));

        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                if (isset($currentEvent['start_date']) && isset($currentEvent['end_date'])) {
                    // Create a booking-like object
                    $booking = (object)[
                        'check_in' => $currentEvent['start_date'],
                        'check_out' => $currentEvent['end_date'],
                        'venue_id' => $venue->id,
                        'source' => $feed ? $feed->source : 'External'
                    ];                    $events->push($booking);
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null && strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);

                if (strpos($key, 'DTSTART') === 0) {
                    $currentEvent['start_date'] = $this->parseIcalDate($value);
                } elseif (strpos($key, 'DTEND') === 0) {
                    $currentEvent['end_date'] = $this->parseIcalDate($value);
                } elseif ($key === 'UID') {
                    $currentEvent['uid'] = $value;
                } elseif ($key === 'SUMMARY') {
                    $currentEvent['summary'] = $value;
                }
            }
        }

        return $events;
    }

    private function parseIcalDate($dateString)
    {
        if (strlen($dateString) === 8) {
            return Carbon::createFromFormat('Ymd', $dateString);
        } elseif (strlen($dateString) === 15 && substr($dateString, -1) === 'Z') {
            return Carbon::createFromFormat('Ymd\THis\Z', $dateString);
        } elseif (strlen($dateString) === 15) {
            return Carbon::createFromFormat('Ymd\THis', $dateString);
        }

        return Carbon::parse($dateString);
    }
}
