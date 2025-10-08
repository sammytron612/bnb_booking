<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Ical;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingApiController extends ApiController
{
    /**
     * Get booked dates for calendar display
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookedDates(Request $request)
    {
        $venueId = $request->query('venue_id'); // Optional venue filter

        // Get confirmed, pending, payment_failed, refunded, and partially refunded bookings (exclude cancelled, payment_expired, and abandoned)
        $bookingsQuery = Booking::whereIn('status', ['confirmed', 'pending', 'payment_failed', 'refunded', 'partial_refund'])
            ->where('check_out', '>=', Carbon::today()) // Only future/current bookings
            ->with('venue')
            ->select('check_in', 'check_out', 'venue_id');

        // Filter by venue if specified
        if ($venueId) {
            $bookingsQuery->where('venue_id', $venueId);
        }

        $bookings = $bookingsQuery->get();

        // Get external bookings from iCal feeds
        $externalBookings = $this->getExternalBookings($venueId);

        $checkInDates = [];      // Dates when guests check in (bookings start)
        $checkOutDates = [];     // Dates when guests check out (bookings end)
        $fullyBookedDates = [];  // Dates that are completely unavailable
        $bookedDates = [];       // All booked dates (for backward compatibility)

        // Process database bookings
        foreach ($bookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);

            // Add check-in date (guests arrive this day at 3pm)
            $checkInDates[] = $checkInDate->format('Y-m-d');

            // Add check-out date (guests leave this day at 11am)
            $checkOutDates[] = $checkOutDate->format('Y-m-d');

            // Add all nights when property is occupied (excluding check-out date)
            // From check-in date up to (but not including) check-out date
            // This represents the nights guests are staying
            $current = $checkInDate->copy();
            while ($current < $checkOutDate) {
                $dateStr = $current->format('Y-m-d');
                $fullyBookedDates[] = $dateStr;
                $bookedDates[] = $dateStr; // For backward compatibility
                $current->addDay();
            }
        }

        // Process external bookings
        foreach ($externalBookings as $booking) {
            // Skip past bookings
            if ($booking->check_out < Carbon::today()) {
                continue;
            }

            $checkInDate = Carbon::parse($booking->check_in);
            $checkOutDate = Carbon::parse($booking->check_out);

            // Add check-in date
            $checkInDates[] = $checkInDate->format('Y-m-d');

            // Add check-out date
            $checkOutDates[] = $checkOutDate->format('Y-m-d');

            // Add all nights when property is occupied (excluding check-out date)
            $current = $checkInDate->copy();
            while ($current < $checkOutDate) {
                $dateStr = $current->format('Y-m-d');
                $fullyBookedDates[] = $dateStr;
                $bookedDates[] = $dateStr; // For backward compatibility
                $current->addDay();
            }
        }

        // Remove duplicates and sort all arrays
        $checkInDates = array_unique($checkInDates);
        $checkOutDates = array_unique($checkOutDates);
        $fullyBookedDates = array_unique($fullyBookedDates);
        $bookedDates = array_unique($bookedDates);

        sort($checkInDates);
        sort($checkOutDates);
        sort($fullyBookedDates);
        sort($bookedDates);

        $response = response()->json([
            'success' => true,
            'checkInDates' => $checkInDates,
            'checkOutDates' => $checkOutDates,
            'fullyBookedDates' => $fullyBookedDates,
            'bookedDates' => $bookedDates, // For backward compatibility
            'count' => count($fullyBookedDates)
        ]);

        return $this->withoutCache($response);
    }

    /**
     * Get external bookings from iCal feeds
     *
     * @param int|null $venueId
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
                    $events = $this->parseIcalEvents($icalData, $feed->venue);
                    $externalBookings = $externalBookings->merge($events);
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
     * Parse iCal events data
     *
     * @param string $icalData
     * @param \App\Models\Venue $venue
     * @return \Illuminate\Support\Collection
     */
    private function parseIcalEvents($icalData, $venue)
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
     * Parse iCal date string
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
