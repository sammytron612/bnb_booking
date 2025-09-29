<?php

namespace App\Services\BookingServices;

use App\Models\Ical;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExternalCalendarService
{
    /**
     * Get all external bookings from iCal feeds
     *
     * @param int|null $venueId Optional venue ID to filter by
     * @return \Illuminate\Support\Collection
     */
    public function getExternalBookings($venueId = null)
    {
        $externalBookings = collect();

        try {
            // Get active iCal feeds
            $icalFeeds = Ical::where('active', true)->with('venue');

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
            Log::warning('Failed to fetch external bookings: ' . $e->getMessage());
        }

        return $externalBookings;
    }

    /**
     * Fetch iCal data from URL
     *
     * @param string $url
     * @return string|null
     */
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

    /**
     * Parse iCal events from iCal data
     *
     * @param string $icalData
     * @param object $venue
     * @param object|null $feed
     * @return \Illuminate\Support\Collection
     */
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
                    ];

                    $events->push($booking);
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

    /**
     * Parse iCal date string into Carbon instance
     *
     * @param string $dateString
     * @return \Carbon\Carbon
     */
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
