<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

use App\Models\Booking;

echo "Checking booking 130:\n";
$booking = Booking::find(130);

if ($booking) {
    echo "ID: " . $booking->id . "\n";
    echo "Booking ID: " . $booking->booking_id . "\n";
    echo "Payment Intent: [" . $booking->stripe_payment_intent_id . "]\n";
    echo "Length: " . strlen($booking->stripe_payment_intent_id) . "\n";

    // Check for whitespace
    echo "Trimmed: [" . trim($booking->stripe_payment_intent_id) . "]\n";

    // Try to find it
    $found = Booking::where('stripe_payment_intent_id', $booking->stripe_payment_intent_id)->first();
    echo "Can find by PI? " . ($found ? "YES" : "NO") . "\n";

    // Try trimmed version
    $foundTrimmed = Booking::where('stripe_payment_intent_id', trim($booking->stripe_payment_intent_id))->first();
    echo "Can find by trimmed PI? " . ($foundTrimmed ? "YES" : "NO") . "\n";

} else {
    echo "Booking not found!\n";
}
