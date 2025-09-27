<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;

class IcalController extends Controller
{
    /**
     * Export venue calendar in iCal format for external services like Airbnb
     */
    public function exportVenueCalendar($venue_id)
    {
        $venue = Venue::find($venue_id);
        
        if (!$venue) {
            abort(404, 'Venue not found');
        }

        // Get all confirmed bookings for this venue
        $bookings = Booking::where('venue_id', $venue_id)
            ->where('status', '!=', 'cancelled')
            ->orderBy('check_in')
            ->get();

        // Generate iCal content
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Eileen BnB//Booking Calendar//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "X-WR-CALNAME:{$venue->venue_name} - Bookings\r\n";
        $ical .= "X-WR-CALDESC:Blocked dates for {$venue->venue_name}\r\n";

        foreach ($bookings as $booking) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:booking-{$booking->id}@eileenbnb.com\r\n";
            $ical .= "DTSTART;VALUE=DATE:" . $booking->check_in->format('Ymd') . "\r\n";
            $ical .= "DTEND;VALUE=DATE:" . $booking->check_out->format('Ymd') . "\r\n";
            $ical .= "DTSTAMP:" . $booking->updated_at->utc()->format('Ymd\THis\Z') . "\r\n";
            $ical .= "SUMMARY:BOOKED: {$venue->venue_name}\r\n";
            $ical .= "DESCRIPTION:Booking for {$venue->venue_name}\r\n";
            $ical .= "STATUS:CONFIRMED\r\n";
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . \Illuminate\Support\Str::slug($venue->venue_name) . '-calendar.ics"')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get venue calendars (placeholder for admin functionality)
     */
    public function getVenueCalendars($venueId)
    {
        return response()->json(['message' => 'Feature coming soon'], 501);
    }

    /**
     * Fetch iCal data (placeholder for admin functionality)
     */
    public function fetchIcalData()
    {
        return response()->json(['message' => 'Feature coming soon'], 501);
    }

    /**
     * Get combined booking data (placeholder for admin functionality)
     */
    public function getCombinedBookingData()
    {
        return response()->json(['message' => 'Feature coming soon'], 501);
    }
}
