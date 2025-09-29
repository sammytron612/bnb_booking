<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check iCal records
$icals = DB::table('ical')->get();

echo "=== iCal Records ===\n";
foreach ($icals as $ical) {
    echo "ID: {$ical->id}\n";
    echo "Venue: {$ical->venue_id}\n";
    echo "Source: {$ical->source}\n";
    echo "Name: {$ical->name}\n";
    echo "Active: " . ($ical->active ? 'Yes' : 'No') . "\n";
    echo "URL: " . substr($ical->url, 0, 80) . (strlen($ical->url) > 80 ? '...' : '') . "\n";
    echo "Last synced: {$ical->last_synced_at}\n";
    echo "---\n";
}

echo "\n=== Testing External Bookings Fetch ===\n";

try {
    $externalCalendarService = $app->make(\App\Services\BookingServices\ExternalCalendarService::class);
    $externalBookings = $externalCalendarService->getExternalBookings();

    echo "Found " . $externalBookings->count() . " external bookings:\n";

    foreach ($externalBookings as $booking) {
        $source = isset($booking->source) ? $booking->source : 'Unknown';
        echo "- Venue {$booking->venue_id}: {$source} ({$booking->check_in->format('Y-m-d')} to {$booking->check_out->format('Y-m-d')})\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Testing BookingsTable External Bookings ===\n";

try {
    $bookingsTable = new \App\Livewire\BookingsTable();
    $method = new ReflectionMethod($bookingsTable, 'getExternalBookings');
    $method->setAccessible(true);
    $tableExternalBookings = $method->invoke($bookingsTable);

    echo "BookingsTable found " . $tableExternalBookings->count() . " external bookings:\n";

    foreach ($tableExternalBookings as $booking) {
        echo "- Venue {$booking->venue_id}: {$booking->name} ({$booking->check_in->format('Y-m-d')} to {$booking->check_out->format('Y-m-d')})\n";
    }

} catch (Exception $e) {
    echo "BookingsTable Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
