<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $ical .= "UID:airbnb-booking-1@eileenbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251001\r\n";
        $ical .= "DTEND;VALUE=DATE:20251003\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Airbnb Booking\r\n";
        $ical .= "DESCRIPTION:Test Airbnb booking for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:airbnb-booking-2@eileenbnb.com\r\n";
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
        $ical .= "UID:booking-com-1@eileenbnb.com\r\n";
        $ical .= "DTSTART;VALUE=DATE:20251005\r\n";
        $ical .= "DTEND;VALUE=DATE:20251007\r\n";
        $ical .= "DTSTAMP:20250930T120000Z\r\n";
        $ical .= "SUMMARY:Booking.com Reservation\r\n";
        $ical .= "DESCRIPTION:Test Booking.com reservation for import\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "END:VEVENT\r\n";

        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:booking-com-2@eileenbnb.com\r\n";
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
}
