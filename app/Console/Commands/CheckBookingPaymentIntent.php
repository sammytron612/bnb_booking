<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class CheckBookingPaymentIntent extends Command
{
    protected $signature = 'booking:check-pi {id}';
    protected $description = 'Check payment intent for a specific booking';

    public function handle()
    {
        $id = $this->argument('id');

        $this->info("Checking booking {$id}:");
        $booking = Booking::find($id);

        if ($booking) {
            $this->info("ID: " . $booking->id);
            $this->info("Booking ID: " . $booking->booking_id);
            $this->info("Payment Intent: [" . $booking->stripe_payment_intent_id . "]");
            $this->info("Length: " . strlen($booking->stripe_payment_intent_id));

            // Check for whitespace
            $this->info("Trimmed: [" . trim($booking->stripe_payment_intent_id) . "]");

            // Try to find it
            $found = Booking::where('stripe_payment_intent_id', $booking->stripe_payment_intent_id)->first();
            $this->info("Can find by PI? " . ($found ? "YES (ID: {$found->id})" : "NO"));

            // Try trimmed version
            $foundTrimmed = Booking::where('stripe_payment_intent_id', trim($booking->stripe_payment_intent_id))->first();
            $this->info("Can find by trimmed PI? " . ($foundTrimmed ? "YES (ID: {$foundTrimmed->id})" : "NO"));

            // Check for exact match
            $target = 'pi_3SFgcZGvUTnoJDRl1jwHGK36';
            $this->info("Target PI: [{$target}]");
            $this->info("Match? " . ($booking->stripe_payment_intent_id === $target ? "YES" : "NO"));

        } else {
            $this->error("Booking not found!");
        }

        return 0;
    }
}
