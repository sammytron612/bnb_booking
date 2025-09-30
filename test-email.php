<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Mail\PaymentExpired;
use Illuminate\Support\Facades\Mail;

try {
    $booking = Booking::where('booking_id', '3632154')->first();

    if (!$booking) {
        echo "❌ Booking not found\n";
        exit(1);
    }

    echo "📧 Testing email for booking: " . $booking->getDisplayBookingId() . "\n";
    echo "📧 Email address: " . $booking->email . "\n";

    // Test email sending - use a real email for testing
    $testEmail = 'sland_man@yahoo.co.uk'; // Use your email for testing
    echo "📧 Sending test email to: " . $testEmail . "\n";

    Mail::to($testEmail)->send(new PaymentExpired($booking));

    echo "✅ Email sent successfully!\n";

} catch (Exception $e) {
    echo "❌ Email error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
