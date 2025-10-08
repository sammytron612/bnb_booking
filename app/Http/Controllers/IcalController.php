<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Str;

class IcalController extends Controller
{
    /**
     * Serve static iCal data for import testing
     */
    public function getHotelIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Eileen BnB//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for test import\r\n";

        // Add a couple of test events
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:test-booking-1@seaham-retreats.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251010\r\n";
        $ical .= "DTEND;VALUE=DATE:20251015\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:BOOKED: Test Venue\r\n";
        $ical .= "DESCRIPTION:Test booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:test-booking-2@seaham-retreats.com\r\n";
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
     * Serve Airbnb test iCal data for import testing
     */
    public function getAirbnbTestIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Airbnb//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Airbnb Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for Airbnb test import\r\n";

        // Add test events for Airbnb
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:airbnb-booking-1@seaham-retreats.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251001\r\n";
        $ical .= "DTEND;VALUE=DATE:20251003\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Airbnb Booking\r\n";
        $ical .= "DESCRIPTION:Test Airbnb booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:airbnb-booking-2@seaham-retreats.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251008\r\n";
        $ical .= "DTEND;VALUE=DATE:20251010\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Airbnb Booking\r\n";
        $ical .= "DESCRIPTION:Second Airbnb test booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="airbnb-test-calendar.ics"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Serve Booking.com test iCal data for import testing
     */
    public function getBookingComTestIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Booking.com//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Booking.com Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for Booking.com test import\r\n";

        // Add test events for Booking.com
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:booking-com-1@seaham-retreats.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251005\r\n";
        $ical .= "DTEND;VALUE=DATE:20251007\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Booking.com Reservation\r\n";
        $ical .= "DESCRIPTION:Test Booking.com reservation for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:booking-com-2@seaham-retreats.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251012\r\n";
        $ical .= "DTEND;VALUE=DATE:20251015\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Booking.com Reservation\r\n";
        $ical .= "DESCRIPTION:Second Booking.com test reservation for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="booking-com-test-calendar.ics"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Export venue calendar for external platform sync (Airbnb, Hotels.com, etc.)
     * Shows real database bookings from today onwards
     */
    public function exportVenueCalendar(Request $request, $venue_id)
    {
        // Validate venue exists
        $venue = Venue::find($venue_id);
        if (!$venue) {
            return response('Venue not found', 404);
        }

        // Get confirmed, pending, and partially refunded bookings from today onwards
        $bookings = Booking::where('venue_id', $venue_id)
            ->whereIn('status', ['confirmed', 'pending', 'partial_refund'])
            ->where('check_out', '>=', Carbon::today())
            ->orderBy('check_in')
            ->get();

        // Build iCal content
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Seaham Retreats//Venue Calendar Export//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:" . $venue->venue_name . " - Blocked Dates\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for venue: " . $venue->venue_name . "\r\n";
        $ical .= "X-WR-TIMEZONE:Europe/London\r\n";

        // Add each booking as a VEVENT
        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);
            $createdAt = Carbon::parse($booking->created_at);

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:booking-" . $booking->id . "@seaham-retreats.com\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $checkIn->format('Ymd') . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $checkOut->format('Ymd') . "\r\n";
            $ical .= "DTSTAMP:" . $createdAt->utc()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "CREATED:" . $createdAt->utc()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "LAST-MODIFIED:" . Carbon::parse($booking->updated_at)->utc()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "SUMMARY:BLOCKED - " . $venue->venue_name . "\r\n";
            $ical .= "DESCRIPTION:Booking ID: " . $booking->id . " (Status: " . ucfirst($booking->status) . ")\r\n";
            $ical .= "STATUS:" . strtoupper($booking->status === 'confirmed' ? 'confirmed' : 'tentative') . "\r\n";
            $ical .= "TRANSP:OPAQUE\r\n"; // Shows as busy/blocked
            $ical .= "CATEGORIES:BOOKING\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        // Generate filename with venue name and current date
        $filename = 'venue-' . $venue_id . '-' . Str::slug($venue->venue_name) . '-calendar.ics';

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type')
            ->header('Cache-Control', 'public, max-age=1800'); // 30 minutes cache
    }
}
