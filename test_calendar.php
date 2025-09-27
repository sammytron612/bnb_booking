<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Calendar Data Generation ===\n";

try {
    $bookingsTable = new \App\Livewire\BookingsTable();
    $calendarData = $bookingsTable->getCalendarData();

    echo "Generated calendar data for " . $calendarData->count() . " days\n";

    // Look for Sept 28 specifically
    $sept28 = $calendarData->first(function ($day) {
        return $day['date']->format('Y-m-d') === '2025-09-28';
    });

    if ($sept28) {
        echo "\nSept 28th found in calendar:\n";
        echo "- Total bookings: " . $sept28['booking_count'] . "\n";
        echo "- Has double booking: " . ($sept28['has_double_booking'] ? 'YES' : 'NO') . "\n";
        echo "- Check-ins: " . $sept28['check_in_count'] . "\n";
        echo "- Check-outs: " . $sept28['check_out_count'] . "\n";

        echo "\nCheck-in sources:\n";
        foreach ($sept28['check_ins'] as $checkIn) {
            echo "  - " . $checkIn->name . "\n";
        }

        echo "\nAll booking sources:\n";
        foreach ($sept28['bookings'] as $booking) {
            echo "  - " . $booking->name . "\n";
        }
    } else {
        echo "\nSept 28th NOT found in calendar range\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
