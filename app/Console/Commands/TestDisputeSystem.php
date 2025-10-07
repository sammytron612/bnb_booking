<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Mail\DisputeNotification;
use Illuminate\Support\Facades\Mail;

class TestDisputeSystem extends Command
{
    protected $signature = 'dispute:test {booking_id?}';
    protected $description = 'Test the dispute system with a fake dispute';

    public function handle()
    {
        $bookingId = $this->argument('booking_id');

        if ($bookingId) {
            $booking = Booking::where('booking_id', $bookingId)->first();
        } else {
            $booking = Booking::where('is_paid', true)->first();
        }

        if (!$booking) {
            $this->error('No paid booking found. Please create a paid booking first or specify a booking ID.');
            return 1;
        }

        $this->info("Testing dispute system with booking: {$booking->booking_id}");
        $this->info("Guest: {$booking->name} ({$booking->email})");
        $this->info("Amount: £" . number_format((float)$booking->total_price, 2));

        // Create a test dispute
        $dispute = BookingDispute::create([
            'booking_id' => $booking->id,
            'stripe_dispute_id' => 'dp_test_' . uniqid(),
            'stripe_charge_id' => 'ch_test_' . uniqid(),
            'amount' => (float)$booking->total_price * 100, // Convert to pence
            'currency' => 'gbp',
            'reason' => 'unrecognized',
            'status' => 'needs_response',
            'evidence_due_by' => now()->addDays(7),
            'created_at_stripe' => now(),
            'admin_notified' => false,
        ]);

        $this->info("✅ Test dispute created: {$dispute->stripe_dispute_id}");

        // Test email notification
        try {
            Mail::to(config('mail.owner_email', 'admin@example.com'))
                ->send(new DisputeNotification($dispute));

            $dispute->update(['admin_notified' => true]);

            $this->info("✅ Email notification sent to: " . config('mail.owner_email'));
            $this->info("📧 Check your email for the dispute notification");
        } catch (\Exception $e) {
            $this->error("❌ Email failed: " . $e->getMessage());
        }

        // Display dispute details
        $this->newLine();
        $this->info("🔍 Dispute Details:");
        $this->table(
            ['Property', 'Value'],
            [
                ['Amount', $dispute->amount_in_pounds],
                ['Reason', $dispute->friendly_reason],
                ['Status', $dispute->friendly_status],
                ['Urgent?', $dispute->is_urgent ? 'Yes' : 'No'],
                ['Days Until Due', $dispute->days_until_due],
                ['Admin Notified', $dispute->admin_notified ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();
        $this->info("💡 To clean up, run: php artisan tinker");
        $this->info("   Then: BookingDispute::where('stripe_dispute_id', '{$dispute->stripe_dispute_id}')->delete()");

        return 0;
    }
}
