<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Venue;

class CreateTestBooking extends Command
{
    protected $signature = 'booking:test-create';
    protected $description = 'Create a test booking for dispute testing';

    public function handle()
    {
        $venue = Venue::first();

        if (!$venue) {
            $this->error('No venue found. Create a venue first.');
            return 1;
        }

        // Create a test booking
        $booking = Booking::create([
            'booking_id' => 'TEST' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => 'Test Dispute User',
            'email' => 'test-dispute@example.com',
            'phone' => '07000000000',
            'check_in' => now()->addDays(30)->format('Y-m-d'),
            'check_out' => now()->addDays(32)->format('Y-m-d'),
            'venue_id' => $venue->id,
            'nights' => 2,
            'total_price' => 50.00, // Small amount for testing
            'status' => 'confirmed',
            'is_paid' => true,
            'stripe_payment_intent_id' => 'pi_test_dispute_' . uniqid(),
            'stripe_amount' => 5000, // £50 in pence
            'stripe_currency' => 'gbp',
            'payment_completed_at' => now(),
            'pay_method' => 'stripe_checkout',
        ]);

        $this->info("✅ Test booking created: {$booking->booking_id}");
        $this->info("💰 Amount: £50.00 (small amount for safe testing)");
        $this->info("📧 Guest: test-dispute@example.com");
        $this->newLine();

        $this->info("🧪 Now you can:");
        $this->info("1. Run: php artisan dispute:test {$booking->booking_id}");
        $this->info("2. Or create a real Stripe test payment and dispute it");
        $this->newLine();

        $this->warn("⚠️  Remember to delete this test booking later:");
        $this->info("   php artisan tinker");
        $this->info("   Booking::where('booking_id', '{$booking->booking_id}')->delete()");

        return 0;
    }
}
