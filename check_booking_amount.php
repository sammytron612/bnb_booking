<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\Booking;

// Find the most recent booking with a stripe_payment_intent_id
$booking = Booking::whereNotNull('stripe_payment_intent_id')
    ->latest()
    ->first(['booking_id', 'total_price', 'stripe_payment_intent_id', 'status']);

if ($booking) {
    echo "Latest booking with Stripe payment:\n";
    echo "Booking ID: " . $booking->booking_id . "\n";
    echo "Total Price: £" . $booking->total_price . "\n";
    echo "Stripe Payment Intent ID: " . $booking->stripe_payment_intent_id . "\n";
    echo "Status: " . $booking->status . "\n";
    echo "\nExpected Stripe amount (in pence): " . ($booking->total_price * 100) . "\n";
} else {
    echo "No bookings found with Stripe payment intent ID\n";
}
