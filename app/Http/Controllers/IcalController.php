<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ical;
use App\Models\Venue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Booking;


class IcalController extends Controller
{
    /**
     * Get all ical calendars for a specific venue
     */
    public function getVenueCalendars($venueId)
    {
        try {
            $venue = Venue::find($venueId);

            if (!$venue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venue not found'
                ], 404);
            }

            $calendars = Ical::where('venue_id', $venueId)
                ->select('id', 'url', 'source', 'name', 'active', 'last_synced')
                ->get();

            return response()->json([
                'success' => true,
                'venue' => [
                    'id' => $venue->id,
                    'name' => $venue->venue_name
                ],
                'calendars' => $calendars
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching venue calendars: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch calendars'
            ], 500);
        }
    }

    /**
     * Fetch and parse iCal data from external sources
     */
    public function fetchIcalData(Request $request)
    {
        $venueId = $request->get('venue_id');
        $source = $request->get('source'); // 'airbnb', 'booking', or null for all

        try {
            $query = Ical::where('venue_id', $venueId)
                ->where('active', true);

            if ($source) {
                $query->where('source', $source);
            }

            $calendars = $query->get();

            if ($calendars->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active calendars found for this venue'
                ], 404);
            }

            $allBookedDates = [];
            $errors = [];

            foreach ($calendars as $calendar) {
                try {
                    $bookedDates = $this->parseIcalFromUrl($calendar->url);

                    if (!empty($bookedDates)) {
                        $allBookedDates = array_merge($allBookedDates, $bookedDates);

                        // Update last synced time
                        $calendar->update(['last_synced' => now()]);
                    }

                } catch (\Exception $e) {
                    $errors[] = [
                        'calendar' => $calendar->name,
                        'source' => $calendar->source,
                        'error' => $e->getMessage()
                    ];

                    Log::error("Failed to fetch iCal data from {$calendar->name}: " . $e->getMessage());
                }
            }

            // Remove duplicates and sort dates
            $allBookedDates = array_unique($allBookedDates);
            sort($allBookedDates);

            return response()->json([
                'success' => true,
                'venue_id' => $venueId,
                'booked_dates' => $allBookedDates,
                'calendars_synced' => $calendars->count() - count($errors),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching iCal data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch iCal data'
            ], 500);
        }
    }

    /**
     * Get combined booking data (database + iCal)
     */
    public function getCombinedBookingData(Request $request)
    {
        $venueId = $request->get('venue_id');

        try {
            // Get database booked dates (you might already have this endpoint)
            $databaseBookings = $this->getDatabaseBookings($venueId);

            // Get iCal booked dates
            $icalResponse = $this->fetchIcalData($request);
            $icalData = json_decode($icalResponse->getContent(), true);

            $icalBookings = $icalData['success'] ? $icalData['booked_dates'] : [];

            // Combine and deduplicate
            $allBookedDates = array_unique(array_merge($databaseBookings, $icalBookings));
            sort($allBookedDates);

            return response()->json([
                'success' => true,
                'venue_id' => $venueId,
                'booked_dates' => $allBookedDates,
                'sources' => [
                    'database_count' => count($databaseBookings),
                    'ical_count' => count($icalBookings),
                    'total_unique' => count($allBookedDates)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting combined booking data: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get booking data'
            ], 500);
        }
    }

    /**
     * Parse iCal data from URL
     */
    private function parseIcalFromUrl($url)
    {
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception("Failed to fetch iCal data: HTTP {$response->status()}");
        }

        $icalContent = $response->body();
        return $this->parseIcalContent($icalContent);
    }

    /**
     * Parse iCal content and extract booked dates
     */
    private function parseIcalContent($icalContent)
    {
        $bookedDates = [];
        $lines = explode("\n", $icalContent);

        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                // Process the event
                if (isset($currentEvent['DTSTART']) && isset($currentEvent['DTEND'])) {
                    $startDate = $this->parseIcalDate($currentEvent['DTSTART']);
                    $endDate = $this->parseIcalDate($currentEvent['DTEND']);

                    if ($startDate && $endDate) {
                        $current = $startDate->copy();
                        while ($current->lt($endDate)) {
                            $bookedDates[] = $current->format('Y-m-d');
                            $current->addDay();
                        }
                    }
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null && strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                // Handle properties with parameters like DTSTART;VALUE=DATE
                $propertyName = explode(';', $key)[0];
                $currentEvent[$propertyName] = $value;
            }
        }

        return array_unique($bookedDates);
    }

    /**
     * Parse iCal date format
     */
    private function parseIcalDate($dateString)
    {
        try {
            // Handle different iCal date formats
            if (strpos($dateString, 'T') !== false) {
                // DateTime format: 20231225T140000Z
                return Carbon::createFromFormat('Ymd\THis\Z', $dateString);
            } else {
                // Date only format: 20231225
                return Carbon::createFromFormat('Ymd', $dateString);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to parse iCal date: {$dateString}");
            return null;
        }
    }

    /**
     * Get database bookings for a venue
     */
    private function getDatabaseBookings($venueId)
    {
        $bookingsQuery = Booking::where('status', '!=', 'cancelled')
            ->where('check_out', '>=', Carbon::today())
            ->select('check_in', 'check_out', 'venue_id');

        if ($venueId) {
            $bookingsQuery->where('venue_id', $venueId);
        }

        $bookings = $bookingsQuery->get();
        $bookedDates = [];

        foreach ($bookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);

            // Add all nights when property is occupied
            $current = $checkInDate->copy();
            while ($current < $checkOutDate) {
                $bookedDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }

        return array_unique($bookedDates);
    }

    /**
     * Export venue bookings as iCal format for external sites to sync (Outlook compatible)
     */
    public function exportVenueCalendar($venueId)
    {
        try {
            $venue = Venue::find($venueId);

            if (!$venue) {
                return response('Venue not found', 404)
                    ->header('Content-Type', 'text/plain');
            }

            // Get all confirmed AND PAID bookings for this venue
            // Include past bookings for full sync (Outlook expects complete history)
            $bookings = Booking::where('venue_id', $venueId)
                ->where('status', 'confirmed')  // Only confirmed bookings
                ->where('is_paid', true)        // Only paid bookings
                ->orderBy('check_in')
                ->get();

            // Generate iCal content
            $icalContent = $this->generateIcalContent($venue, $bookings);

            // Ensure proper UTF-8 encoding for Outlook
            $icalContent = mb_convert_encoding($icalContent, 'UTF-8', 'UTF-8');

            // Outlook-compatible headers with proper MIME type
            return response($icalContent)
                ->header('Content-Type', 'text/calendar; charset=utf-8; method=PUBLISH')
                ->header('Content-Disposition', 'attachment; filename="' . \Str::slug($venue->venue_name) . '.ics"')
                ->header('Content-Length', strlen($icalContent))
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS, HEAD')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->header('Vary', 'Accept-Encoding');

        } catch (\Exception $e) {
            \Log::error('Error exporting venue calendar: ' . $e->getMessage(), [
                'venue_id' => $venueId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response('Error generating calendar', 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Generate Outlook-compatible iCal content from bookings
     */
    private function generateIcalContent($venue, $bookings)
    {
        $ical = [];

        // Outlook-compatible iCal header
        $ical[] = 'BEGIN:VCALENDAR';
        $ical[] = 'VERSION:2.0';
        $ical[] = 'PRODID:-//Seaham Coastal Retreats//Booking Calendar//EN';
        $ical[] = 'CALSCALE:GREGORIAN';
        $ical[] = 'METHOD:PUBLISH';

        // Outlook-friendly calendar name (no special characters)
        $calendarName = $this->sanitizeCalendarText($venue->venue_name . ' Bookings');
        $calendarDesc = $this->sanitizeCalendarText('Blocked dates for ' . $venue->venue_name);

        $ical[] = 'X-WR-CALNAME:' . $calendarName;
        $ical[] = 'X-WR-CALDESC:' . $calendarDesc;
        $ical[] = 'X-WR-TIMEZONE:Europe/London';

        // Add timezone info for Outlook compatibility
        $ical[] = 'BEGIN:VTIMEZONE';
        $ical[] = 'TZID:Europe/London';
        $ical[] = 'BEGIN:STANDARD';
        $ical[] = 'DTSTART:20071028T020000';
        $ical[] = 'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU';
        $ical[] = 'TZNAME:GMT';
        $ical[] = 'TZOFFSETFROM:+0100';
        $ical[] = 'TZOFFSETTO:+0000';
        $ical[] = 'END:STANDARD';
        $ical[] = 'BEGIN:DAYLIGHT';
        $ical[] = 'DTSTART:20070325T010000';
        $ical[] = 'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU';
        $ical[] = 'TZNAME:BST';
        $ical[] = 'TZOFFSETFROM:+0000';
        $ical[] = 'TZOFFSETTO:+0100';
        $ical[] = 'END:DAYLIGHT';
        $ical[] = 'END:VTIMEZONE';

        // Add each booking as an event (Outlook compatible)
        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);

            // Create unique event ID (Outlook compatible)
            $eventId = 'booking-' . $booking->id . '-' . time() . '@seahamcoastalretreats.com';

            // Sanitize text fields for Outlook
            $guestName = $this->sanitizeCalendarText($booking->name ?? 'Guest');
            $summary = $this->sanitizeCalendarText('BLOCKED - ' . $guestName);
            $description = $this->sanitizeCalendarText('Property booked: ' . $venue->venue_name);

            // Outlook-compatible iCal event
            $ical[] = 'BEGIN:VEVENT';
            $ical[] = 'UID:' . $eventId;
            $ical[] = 'DTSTART;VALUE=DATE:' . $checkIn->format('Ymd');
            $ical[] = 'DTEND;VALUE=DATE:' . $checkOut->format('Ymd');
            $ical[] = 'SUMMARY:' . $summary;
            $ical[] = 'DESCRIPTION:' . $description;
            $ical[] = 'STATUS:CONFIRMED';
            $ical[] = 'TRANSP:OPAQUE';
            $ical[] = 'CLASS:PRIVATE';

            // Add current timestamp for Outlook sync
            $now = Carbon::now()->utc()->format('Ymd\THis\Z');
            $created = Carbon::parse($booking->created_at)->utc()->format('Ymd\THis\Z');
            $modified = Carbon::parse($booking->updated_at)->utc()->format('Ymd\THis\Z');

            $ical[] = 'DTSTAMP:' . $now;
            $ical[] = 'CREATED:' . $created;
            $ical[] = 'LAST-MODIFIED:' . $modified;
            $ical[] = 'SEQUENCE:0';

            $ical[] = 'END:VEVENT';
        }

        // iCal footer
        $ical[] = 'END:VCALENDAR';

        // Join with proper iCal line endings (CRLF) and fold long lines for Outlook compatibility
        $content = implode("\r\n", $ical);

        // Fold lines longer than 75 characters (RFC 5545 compliance for Outlook)
        $lines = explode("\r\n", $content);
        $foldedLines = [];

        foreach ($lines as $line) {
            if (strlen($line) <= 75) {
                $foldedLines[] = $line;
            } else {
                // Fold long lines by splitting at 75 characters and continuing on next line with space
                $foldedLines[] = substr($line, 0, 75);
                $remainder = substr($line, 75);
                while (strlen($remainder) > 74) {
                    $foldedLines[] = ' ' . substr($remainder, 0, 74);
                    $remainder = substr($remainder, 74);
                }
                if (strlen($remainder) > 0) {
                    $foldedLines[] = ' ' . $remainder;
                }
            }
        }

        return implode("\r\n", $foldedLines);
    }

    /**
     * Sanitize text for iCal compatibility (especially Outlook)
     */
    private function sanitizeCalendarText($text)
    {
        // Remove or escape problematic characters for RFC 5545 compliance
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        $text = str_replace([',', ';', '\\'], ['\\,', '\\;', '\\\\'], $text);
        $text = preg_replace('/\s+/', ' ', $text); // Multiple spaces to single
        $text = trim($text);

        // Limit length to prevent issues
        if (strlen($text) > 70) {
            $text = substr($text, 0, 67) . '...';
        }

        return $text;
    }
}
