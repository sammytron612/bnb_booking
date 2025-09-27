<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IcalController extends Controller
{
    /**
     * Serve static Airbnb iCal data for import testing
     */
    public function getAirbnbTestIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Airbnb//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Airbnb Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for Airbnb test import\r\n";

        // Add Airbnb test events
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:airbnb-booking-1@airbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20250928\r\n";
        $ical .= "DTEND;VALUE=DATE:20251002\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:Airbnb reservation (Not available)\r\n";
        $ical .= "DESCRIPTION:Airbnb booking for test import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:airbnb-booking-2@airbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251005\r\n";
        $ical .= "DTEND;VALUE=DATE:20251007\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:Airbnb reservation (Not available)\r\n";
        $ical .= "DESCRIPTION:Second Airbnb booking for test import\r\n";
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
     * Serve static Booking.com iCal data for import testing
     */
    public function getBookingComTestIcalData(Request $request)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Booking.com//Test Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:Booking.com Test Venue - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for Booking.com test import\r\n";

        // Add Booking.com test events with different dates
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:booking-com-1@booking.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251010\r\n";
        $ical .= "DTEND;VALUE=DATE:20251014\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:Booking.com Reservation\r\n";
        $ical .= "DESCRIPTION:Booking.com reservation for test import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:booking-com-2@booking.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251020\r\n";
        $ical .= "DTEND;VALUE=DATE:20251023\r\n";
        $ical .= "DTSTAMP:20250927T120000Z\r\n";
        $ical .= "SUMMARY:Booking.com Reservation\r\n";
        $ical .= "DESCRIPTION:Second Booking.com reservation for test import\r\n";
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
     * Export venue bookings as iCal feed for external platform sync
     */
    public function exportVenueCalendar(Request $request, $venue_id)
    {
        $venue = \App\Models\Venue::findOrFail($venue_id);

        // Get all confirmed and pending bookings for the venue
        $bookings = \App\Models\Booking::where('venue_id', $venue_id)
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('check_in')
            ->get();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Eileen BnB//Venue Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:" . $venue->venue_name . " - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for " . $venue->venue_name . "\r\n";

        foreach ($bookings as $booking) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:booking-" . $booking->id . "@eileenbnb.com\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $booking->check_in->format('Ymd') . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $booking->check_out->format('Ymd') . "\r\n";
            $ical .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "SUMMARY:BOOKED: " . $venue->venue_name . "\r\n";
            $ical .= "DESCRIPTION:Guest: " . $booking->first_name . " " . $booking->last_name . "\\nStatus: " . ucfirst($booking->status) . "\r\n";
            $ical .= "STATUS:CONFIRMED\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        $filename = strtolower(str_replace(' ', '-', $venue->venue_name)) . '-calendar.ics';

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }
}
