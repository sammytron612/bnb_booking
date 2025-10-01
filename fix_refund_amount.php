<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;

// Find the specific booking
$booking = Booking::where('booking_id', '2137082')->first();

if ($booking) {
    echo "Found booking: " . $booking->booking_id . "\n";
    echo "Current refund_amount: " . ($booking->refund_amount ?? 'NULL') . "\n";
    echo "Status: " . $booking->status . "\n";

    // Update the refund_amount to 240.00
    $booking->update([
        'refund_amount' => 240.00,
        'refunded_at' => now()
    ]);

    echo "Updated refund_amount to: " . $booking->fresh()->refund_amount . "\n";
} else {
    echo "Booking not found\n";
}
