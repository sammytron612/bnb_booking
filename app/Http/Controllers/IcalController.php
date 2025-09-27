<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Booking;
use Carbon\Carbon;

class IcalController extends Controller
{
    /**
     * Serve static iCal data for import testing
     */
    public function getTestIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Eileen BnB//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for test import\r\n";

        // Add a couple of test events
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:test-booking-1@eileenbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20250928\r\n";
        $ical .= "DTEND;VALUE=DATE:20251002\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:BOOKED: Test Venue\r\n";
        $ical .= "DESCRIPTION:Test booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:test-booking-2@eileenbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251005\r\n";
        $ical .= "DTEND;VALUE=DATE:20251007\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:BOOKED: Test Venue\r\n";
        $ical .= "DESCRIPTION:Second test booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="test-calendar.ics"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Export venue calendar as iCal for external platforms (Airbnb, Booking.com, etc.)
     * This generates an iCal feed of your confirmed bookings that external platforms can import
     */
    public function exportVenueCalendar($venue_id)
    {
        try {
            $venue = Venue::findOrFail($venue_id);

            // Get all confirmed and pending bookings for this venue (not cancelled)
            $bookings = Booking::where('venue_id', $venue_id)
                ->whereIn('status', ['confirmed', 'pending'])
                ->orderBy('check_in')
                ->get();

            // Generate iCal content
            $ical = "BEGIN:VCALENDAR\r\n";
            $ical .= "VERSION:2.0\r\n";
            $ical .= "PRODID:-//Seaham Coastal Retreats//Booking Calendar//EN\r\n";
            $ical .= "CALSCALE:GREGORIAN\r\n";
            $ical .= "X-WR-CALNAME:{$venue->venue_name} - Blocked Dates\r\n";
            $ical .= "X-WR-CALDESC:Blocked dates for {$venue->venue_name} - Import this into Airbnb/Booking.com\r\n";

            foreach ($bookings as $booking) {
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);

                $ical .= "BEGIN:VEVENT\r\n";
                $ical .= "UID:booking-{$booking->id}-{$venue->id}@seahamcoastalretreats.com\r\n";
                $ical .= "DTSTART;VALUE=DATE:{$checkIn->format('Ymd')}\r\n";
                $ical .= "DTEND;VALUE=DATE:{$checkOut->format('Ymd')}\r\n";
                $ical .= "DTSTAMP:" . now()->utc()->format('Ymd\\THis\\Z') . "\r\n";
                $ical .= "SUMMARY:BLOCKED - {$venue->venue_name}\r\n";
                $ical .= "DESCRIPTION:Booking Reference: {$booking->booking_id}\\nGuest: {$booking->name}\\nStatus: {$booking->status}\r\n";
                $ical .= "STATUS:CONFIRMED\r\n";
                $ical .= "TRANSP:OPAQUE\r\n"; // Shows as busy/blocked
                $ical .= "END:VEVENT\r\n";
            }

            $ical .= "END:VCALENDAR\r\n";

            // Generate filename safe for downloads
            $filename = strtolower(str_replace([' ', "'", '"'], ['-', '', ''], $venue->venue_name)) . '-blocked-dates.ics';

            return response($ical)
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Cache-Control', 'public, max-age=1800'); // 30 minute cache

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Venue not found or export failed',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get venue calendars - returns JSON list of venues for calendar management
     */
    public function getVenueCalendars($venueId)
    {
        try {
            $venue = Venue::findOrFail($venueId);

            // Count current bookings
            $bookingCount = Booking::where('venue_id', $venueId)
                ->whereIn('status', ['confirmed', 'pending'])
                ->count();

            // Get export URL for this venue
            $exportUrl = route('api.ical.export', ['venue_id' => $venueId]);

            return response()->json([
                'success' => true,
                'venue' => [
                    'id' => $venue->id,
                    'name' => $venue->venue_name,
                    'booking_count' => $bookingCount,
                    'export_url' => $exportUrl,
                    'instructions' => [
                        'airbnb' => 'Copy the export_url and paste it into Airbnb Calendar Import',
                        'booking_com' => 'Use export_url in Booking.com Calendar Sync settings',
                        'manual' => 'Download the .ics file and upload to your platform'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Venue not found: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Fetch external iCal data - placeholder for future import functionality
     */
    public function fetchIcalData(Request $request)
    {
        return response()->json([
            'message' => 'iCal import functionality - coming soon',
            'note' => 'This endpoint will be used to import external calendars in the future'
        ]);
    }

    /**
     * Get combined booking data - returns both database bookings and export info
     */
    public function getCombinedBookingData(Request $request)
    {
        try {
            $venueId = $request->get('venue_id');
            $startDate = $request->get('start_date', now()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->addDays(365)->format('Y-m-d'));

            $query = Booking::with('venue')
                ->whereIn('status', ['confirmed', 'pending'])
                ->whereBetween('check_in', [$startDate, $endDate]);

            if ($venueId) {
                $query->where('venue_id', $venueId);
            }

            $bookings = $query->orderBy('check_in')->get();

            return response()->json([
                'success' => true,
                'bookings' => $bookings,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'total_bookings' => $bookings->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching booking data: ' . $e->getMessage()
            ], 500);
        }
    }
}
