<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ParseIcalUrl extends Command
{
    protected $signature = 'ical:parse {url}';
    protected $description = 'Parse iCal URL and display booking dates';

    public function handle()
    {
        $url = $this->argument('url');

        $this->info("Fetching iCal data from: {$url}");

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error("Failed to fetch iCal data. HTTP Status: " . $response->status());
                return 1;
            }

            $icalData = $response->body();

            // Debug: Show what we received
            $this->info("Response received (" . strlen($icalData) . " characters)");
            $this->line("First 500 characters:");
            $this->line(substr($icalData, 0, 500));
            $this->line("---");

            $this->parseIcalData($icalData);

        } catch (\Exception $e) {
            $this->error("Error fetching iCal data: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function parseIcalData($icalData)
    {
        $lines = explode("\n", $icalData);
        $events = [];
        $currentEvent = [];
        $inEvent = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === 'BEGIN:VEVENT') {
                $inEvent = true;
                $currentEvent = [];
            } elseif ($line === 'END:VEVENT') {
                $inEvent = false;
                if (!empty($currentEvent)) {
                    $events[] = $currentEvent;
                }
            } elseif ($inEvent) {
                if (strpos($line, ':') !== false) {
                    [$key, $value] = explode(':', $line, 2);
                    $currentEvent[$key] = $value;
                }
            }
        }

        $this->info("\n📅 Found " . count($events) . " booking events:\n");

        // Debug: Show raw events
        if (count($events) === 0) {
            $this->warn("No VEVENT blocks found in iCal data.");
            $this->line("Looking for these patterns in the data:");
            $this->line("- BEGIN:VEVENT");
            $this->line("- END:VEVENT");
            $this->line("- DTSTART");
            $this->line("- DTEND");

            // Show line count and sample lines
            $lines = explode("\n", $icalData);
            $this->line("\nTotal lines: " . count($lines));
            $this->line("Sample lines:");
            foreach (array_slice($lines, 0, 10) as $i => $line) {
                $this->line("Line " . ($i + 1) . ": " . trim($line));
            }
            return;
        }

        $this->line("Raw events found: " . count($events));
        foreach ($events as $i => $event) {
            $this->line("Event " . ($i + 1) . " keys: " . implode(', ', array_keys($event)));
        }

        $bookings = [];
        foreach ($events as $index => $event) {
            // Handle different date field formats
            $checkInDate = $event['DTSTART;VALUE=DATE'] ?? $event['DTSTART'] ?? '';
            $checkOutDate = $event['DTEND;VALUE=DATE'] ?? $event['DTEND'] ?? '';

            $this->line("Event " . ($index + 1) . " - Raw Check-in: '$checkInDate', Raw Check-out: '$checkOutDate'");
            $this->line("  Check-in length: " . strlen($checkInDate) . ", Check-out length: " . strlen($checkOutDate));

            $checkIn = $this->parseDate($checkInDate);
            $checkOut = $this->parseDate($checkOutDate);
            $summary = $event['SUMMARY'] ?? 'Booking';
            $uid = $event['UID'] ?? '';

            $this->line("  Parsed Check-in: " . ($checkIn ? $checkIn->format('Y-m-d') : 'NULL') .
                      ", Parsed Check-out: " . ($checkOut ? $checkOut->format('Y-m-d') : 'NULL'));

            if ($checkIn && $checkOut) {
                $booking = [
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'nights' => $checkIn->diffInDays($checkOut),
                    'summary' => $summary,
                    'uid' => $uid
                ];

                $bookings[] = $booking;
                $this->info("✓ Parsed successfully");
            } else {
                $this->warn("✗ Could not parse dates");
            }
        }

        $this->info("\n📈 Processing Summary:");
        $this->line("• Total events found: " . count($events));
        $this->line("• Successfully parsed bookings: " . count($bookings));
        $this->line("• Failed to parse: " . (count($events) - count($bookings)));

        if (empty($bookings)) {
            $this->warn("No valid booking dates found in the iCal data.");
            return;
        }

        // Sort bookings by check-in date
        usort($bookings, function($a, $b) {
            return $a['check_in'] <=> $b['check_in'];
        });

        // Display bookings in a nice table format
        $this->info("\n📋 All Bookings:");
        $tableData = [];
        foreach ($bookings as $index => $booking) {
            $tableData[] = [
                '#' => $index + 1,
                'Check-in' => $booking['check_in']->format('d/m/Y (D)'),
                'Check-out' => $booking['check_out']->format('d/m/Y (D)'),
                'Nights' => $booking['nights'],
                'Status' => $booking['check_in']->isPast() ? '✅ Past' : ($booking['check_in']->isToday() ? '🔄 Today' : '📅 Future'),
                'Summary' => $this->truncate($booking['summary'], 30)
            ];
        }

        $this->table(
            ['#', 'Check-in', 'Check-out', 'Nights', 'Status', 'Summary'],
            $tableData
        );

        // Show summary statistics
        $pastBookings = collect($bookings)->filter(fn($b) => $b['check_in']->isPast())->count();
        $futureBookings = collect($bookings)->filter(fn($b) => $b['check_in']->isFuture())->count();
        $todayBookings = collect($bookings)->filter(fn($b) => $b['check_in']->isToday())->count();
        $totalNights = collect($bookings)->sum('nights');

        $this->info("\n📊 Summary:");
        $this->line("• Total bookings: " . count($bookings));
        $this->line("• Past bookings: {$pastBookings}");
        $this->line("• Today's bookings: {$todayBookings}");
        $this->line("• Future bookings: {$futureBookings}");
        $this->line("• Total nights: {$totalNights}");

        // Show upcoming bookings in detail
        $upcoming = collect($bookings)->filter(fn($b) => $b['check_in']->isFuture())->take(5);
        if ($upcoming->count() > 0) {
            $this->info("\n🔮 Next 5 upcoming bookings:");
            foreach ($upcoming as $booking) {
                $daysUntil = now()->startOfDay()->diffInDays($booking['check_in']->startOfDay(), false);
                $this->line("• {$booking['check_in']->format('d/m/Y')} - {$booking['check_out']->format('d/m/Y')} ({$booking['nights']} nights) - in {$daysUntil} days");
            }
        }

        $this->info("\n✅ Calendar data parsed successfully!");
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            $this->line("    parseDate: Empty date string");
            return null;
        }

        $this->line("    parseDate: Trying to parse '$dateString' (length: " . strlen($dateString) . ")");

        try {
            // Handle different iCal date formats
            if (strlen($dateString) === 8 && ctype_digit($dateString)) {
                // YYYYMMDD format (pure digits)
                $this->line("    parseDate: Treating as YYYYMMDD format");
                $parsed = Carbon::createFromFormat('Ymd', $dateString);
                $this->line("    parseDate: Successfully parsed to " . $parsed->format('Y-m-d'));
                return $parsed;
            } elseif (strlen($dateString) === 15 && substr($dateString, -1) === 'Z') {
                // YYYYMMDDTHHMMSSZ format
                $this->line("    parseDate: Treating as YYYYMMDDTHHMMSSZ format");
                return Carbon::createFromFormat('Ymd\THis\Z', $dateString);
            } elseif (strlen($dateString) === 15) {
                // YYYYMMDDTHHMMSS format
                $this->line("    parseDate: Treating as YYYYMMDDTHHMMSS format");
                return Carbon::createFromFormat('Ymd\THis', $dateString);
            } else {
                $this->line("    parseDate: Unrecognized format - length " . strlen($dateString) . ", is digits: " . (ctype_digit($dateString) ? 'yes' : 'no'));
            }

            return null;
        } catch (\Exception $e) {
            $this->line("    parseDate: Exception - " . $e->getMessage());
            return null;
        }
    }

    private function truncate($string, $length)
    {
        return strlen($string) > $length ? substr($string, 0, $length - 3) . '...' : $string;
    }
}
