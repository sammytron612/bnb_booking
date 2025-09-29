<?php

namespace App\Services\BookingServices;

use App\Models\Ical;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExternalCalendarService
{
    public function getExternalBookings(): Collection
    {
        $externalBookings = collect();

        // Get all active iCal feeds
        $icals = Ical::where('active', true)->get();

        foreach ($icals as $ical) {
            try {
                // Fetch the iCal content
                $icalContent = $this->fetchIcalContent($ical->url);

                if ($icalContent) {
                    // Parse the iCal content
                    $events = $this->parseIcalContent($icalContent);

                    foreach ($events as $event) {
                        $externalBookings->push((object) [
                            'venue_id' => $ical->venue_id,
                            'source' => $ical->source,
                            'check_in' => $event['start'],
                            'check_out' => $event['end'],
                            'summary' => $event['summary'] ?? 'External Booking'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to fetch iCal from {$ical->source}: " . $e->getMessage());
            }
        }

        return $externalBookings;
    }

    private function fetchIcalContent(string $url): ?string
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
            \Log::warning("Failed to fetch iCal content from {$url}: " . $e->getMessage());
            return null;
        }
    }

    private function parseIcalContent(string $icalContent): array
    {
        $events = [];
        $lines = explode("\n", $icalContent);
        $currentEvent = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT' && $currentEvent !== null) {
                if (isset($currentEvent['start']) && isset($currentEvent['end'])) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null && strpos($line, ':') !== false) {
                [$key, $value] = explode(':', $line, 2);
                $key = strtolower($key);

                if ($key === 'dtstart' || $key === 'dtstart;value=date') {
                    $currentEvent['start'] = $this->parseIcalDate($value);
                } elseif ($key === 'dtend' || $key === 'dtend;value=date') {
                    $currentEvent['end'] = $this->parseIcalDate($value);
                } elseif ($key === 'summary') {
                    $currentEvent['summary'] = $value;
                }
            }
        }

        return $events;
    }

    private function parseIcalDate(string $dateString): Carbon
    {
        // Remove any timezone info for simplicity
        $dateString = preg_replace('/;.*$/', '', $dateString);

        // Handle different date formats
        if (strlen($dateString) === 8) {
            // Date only format: YYYYMMDD
            return Carbon::createFromFormat('Ymd', $dateString)->startOfDay();
        } elseif (strlen($dateString) === 15 && substr($dateString, -1) === 'Z') {
            // UTC datetime format: YYYYMMDDTHHMMSSZ
            return Carbon::createFromFormat('Ymd\THis\Z', $dateString);
        } elseif (strlen($dateString) === 15) {
            // Local datetime format: YYYYMMDDTHHMMSS
            return Carbon::createFromFormat('Ymd\THis', $dateString);
        }

        // Fallback to current date if parsing fails
        \Log::warning("Could not parse iCal date: {$dateString}");
        return Carbon::now();
    }
}
