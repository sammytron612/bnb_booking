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
     * Export venue bookings as iCal format for external sites to sync
     */
    public function exportVenueCalendar($venueId)
    {
        try {
            $venue = Venue::find($venueId);

            if (!$venue) {
                return response('Venue not found', 404)
                    ->header('Content-Type', 'text/plain');
            }

            // Get all confirmed bookings for this venue (future and current only)
            $bookings = Booking::where('venue_id', $venueId)
                ->where('status', '!=', 'cancelled')
                ->whereDate('check_out', '>=', Carbon::today())
                ->orderBy('check_in')
                ->get();

            // Generate iCal content
            $icalContent = $this->generateIcalContent($venue, $bookings);

            return response($icalContent)
                ->header('Content-Type', 'text/calendar; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . \Str::slug($venue->venue_name) . '.ics"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            \Log::error('Error exporting venue calendar: ' . $e->getMessage());

            return response('Error generating calendar', 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    /**
     * Generate iCal content from bookings
     */
    private function generateIcalContent($venue, $bookings)
    {
        $ical = [];

        // iCal header
        $ical[] = 'BEGIN:VCALENDAR';
        $ical[] = 'VERSION:2.0';
        $ical[] = 'PRODID:-//Eileen BnB//Booking Calendar//EN';
        $ical[] = 'CALSCALE:GREGORIAN';
        $ical[] = 'METHOD:PUBLISH';
        $ical[] = 'X-WR-CALNAME:' . $venue->venue_name . ' - Bookings';
        $ical[] = 'X-WR-CALDESC:Booking calendar for ' . $venue->venue_name;
        $ical[] = 'X-WR-TIMEZONE:UTC';

        // Add each booking as an event
        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in);
            $checkOut = Carbon::parse($booking->check_out);

            // Create unique event ID
            $eventId = 'booking-' . $booking->id . '@eileen-bnb.com';

            // iCal event
            $ical[] = 'BEGIN:VEVENT';
            $ical[] = 'UID:' . $eventId;
            $ical[] = 'DTSTART;VALUE=DATE:' . $checkIn->format('Ymd');
            $ical[] = 'DTEND;VALUE=DATE:' . $checkOut->format('Ymd');
            $ical[] = 'SUMMARY:Booked - ' . ($booking->name ?? 'Guest');
            $ical[] = 'DESCRIPTION:Booking #' . $booking->getDisplayBookingId() . ' - ' . $venue->venue_name;
            $ical[] = 'STATUS:CONFIRMED';
            $ical[] = 'TRANSP:OPAQUE';

            // Add creation and modification timestamps
            $created = Carbon::parse($booking->created_at)->utc()->format('Ymd\THis\Z');
            $modified = Carbon::parse($booking->updated_at)->utc()->format('Ymd\THis\Z');
            $ical[] = 'CREATED:' . $created;
            $ical[] = 'LAST-MODIFIED:' . $modified;

            $ical[] = 'END:VEVENT';
        }

        // iCal footer
        $ical[] = 'END:VCALENDAR';

        return implode("\r\n", $ical);
    }
}
