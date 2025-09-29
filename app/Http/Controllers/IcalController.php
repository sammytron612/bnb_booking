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
}
