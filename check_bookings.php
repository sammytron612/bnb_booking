<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$service = new \App\Services\BookingServices\ExternalCalendarService();
$bookings = $service->getExternalBookings();

echo "External bookings:\n";
foreach ($bookings as $booking) {
    echo "Check-in: " . $booking->check_in->format('Y-m-d') .
         " Check-out: " . $booking->check_out->format('Y-m-d') .
         " Venue: " . ($booking->venue_id ?? 'NULL') .
         " Source: " . ($booking->source ?? 'Unknown') . "\n";
}
