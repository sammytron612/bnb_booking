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
    public function exportVenueCalendar(Request $request, $venue_id)
    {
        // Handle CORS preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('')
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

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
     * Get venue calendars and their sync status
     */
    public function getVenueCalendars($venueId)
    {
        $venue = Venue::find($venueId);
        if (!$venue) {
            return response()->json(['error' => 'Venue not found'], 404);
        }

        $icalFeeds = $venue->icalFeeds()->get();

        return response()->json([
            'venue' => $venue->only(['id', 'venue_name']),
            'ical_feeds' => $icalFeeds
        ]);
    }

    /**
     * Fetch and parse iCal data from external URLs
     */
    public function fetchIcalData(Request $request)
    {
        $venueId = $request->get('venue_id');

        if ($venueId) {
            return $this->syncVenueIcalFeeds($venueId);
        }

        return $this->syncAllIcalFeeds();
    }

    /**
     * Get combined booking data including external bookings
     */
    public function getCombinedBookingData(Request $request)
    {
        $venueId = $request->get('venue_id');

        // Sync external bookings first
        if ($venueId) {
            $this->syncVenueIcalFeeds($venueId);
        } else {
            $this->syncAllIcalFeeds();
        }

        // Get local bookings
        $query = Booking::with('venue');

        if ($venueId) {
            $query->where('venue_id', $venueId);
        }

        $bookings = $query->where('status', '!=', 'cancelled')
                         ->orderBy('check_in')
                         ->get();

        return response()->json([
            'bookings' => $bookings,
            'synced_at' => now()->toISOString()
        ]);
    }

    /**
     * Sync iCal feeds for a specific venue
     */
    private function syncVenueIcalFeeds($venueId)
    {
        $venue = Venue::find($venueId);
        if (!$venue) {
            return response()->json(['error' => 'Venue not found'], 404);
        }

        $icalFeeds = $venue->icalFeeds()->where('active', true)->get();
        $totalSynced = 0;
        $errors = [];

        foreach ($icalFeeds as $feed) {
            try {
                $bookings = $this->parseIcalFromUrl($feed->url, $venue);
                $feed->updateSyncStats(count($bookings));
                $totalSynced += count($bookings);
            } catch (\Exception $e) {
                $errors[] = [
                    'feed' => $feed->name,
                    'error' => $e->getMessage()
                ];
                $feed->updateSyncStats(0);
            }
        }

        return response()->json([
            'venue' => $venue->venue_name,
            'feeds_synced' => $icalFeeds->count(),
            'bookings_synced' => $totalSynced,
            'errors' => $errors
        ]);
    }

    /**
     * Sync all active iCal feeds
     */
    private function syncAllIcalFeeds()
    {
        $icalFeeds = \App\Models\Ical::where('active', true)->with('venue')->get();
        $totalSynced = 0;
        $errors = [];

        foreach ($icalFeeds as $feed) {
            try {
                $bookings = $this->parseIcalFromUrl($feed->url, $feed->venue);
                $feed->updateSyncStats(count($bookings));
                $totalSynced += count($bookings);
            } catch (\Exception $e) {
                $errors[] = [
                    'feed' => $feed->name,
                    'venue' => $feed->venue->venue_name,
                    'error' => $e->getMessage()
                ];
                $feed->updateSyncStats(0);
            }
        }

        return response()->json([
            'feeds_synced' => $icalFeeds->count(),
            'bookings_synced' => $totalSynced,
            'errors' => $errors
        ]);
    }

    /**
     * Parse iCal data from URL and create/update bookings
     */
    private function parseIcalFromUrl($url, $venue)
    {
        // Fetch iCal data
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'Eileen BnB Calendar Sync/1.0'
            ]
        ]);

        $icalData = file_get_contents($url, false, $context);

        if ($icalData === false) {
            throw new \Exception('Failed to fetch iCal data from URL');
        }

        return $this->parseIcalData($icalData, $venue);
    }

    /**
     * Parse iCal content and create booking records
     */
    private function parseIcalData($icalData, $venue)
    {
        $bookings = [];
        $events = $this->extractEventsFromIcal($icalData);

        foreach ($events as $event) {
            // Create unique identifier for external bookings
            $externalBookingId = 'EXT-' . strtoupper(substr(md5(($event['uid'] ?? uniqid()) . $venue->id), 0, 8));

            // Create or update booking from external source
            $booking = Booking::updateOrCreate(
                [
                    'venue_id' => $venue->id,
                    'booking_id' => $externalBookingId,
                    'check_in' => $event['start_date'],
                    'check_out' => $event['end_date']
                ],
                [
                    'name' => 'External Booking',
                    'email' => 'external@booking.com',
                    'phone' => '',
                    'total_price' => 0,
                    'status' => 'confirmed',
                    'notes' => 'Imported from external calendar: ' . ($event['summary'] ?? ''),
                    'nights' => $event['start_date']->diffInDays($event['end_date']),
                    'pay_method' => 'external',
                    'is_paid' => true
                ]
            );

            $bookings[] = $booking;
        }

        return $bookings;
    }

    /**
     * Extract events from iCal data
     */
    private function extractEventsFromIcal($icalData)
    {
        $events = [];
        $lines = explode("\r\n", str_replace(["\r\n", "\r", "\n"], "\r\n", $icalData));

        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                if (isset($currentEvent['start_date']) && isset($currentEvent['end_date'])) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);

                    // Handle date fields
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
        }

        return $events;
    }

    /**
     * Parse iCal date format to Carbon instance
     */
    private function parseIcalDate($dateString)
    {
        // Handle different date formats
        if (strlen($dateString) === 8) {
            // YYYYMMDD format
            return Carbon::createFromFormat('Ymd', $dateString);
        } elseif (strlen($dateString) === 15 && substr($dateString, -1) === 'Z') {
            // YYYYMMDDTHHMMSSZ format
            return Carbon::createFromFormat('Ymd\THis\Z', $dateString);
        } elseif (strlen($dateString) === 15) {
            // YYYYMMDDTHHMMSS format
            return Carbon::createFromFormat('Ymd\THis', $dateString);
        }

        // Fallback to parsing any recognizable date format
        return Carbon::parse($dateString);
    }
}
