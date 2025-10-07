<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentServices\WebhookService;
use App\Models\Booking;

class TestDisputeWebhook extends Command
{
    protected $signature = 'dispute:webhook {booking_id?}';
    protected $description = 'Simulate a Stripe dispute webhook for testing';

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

        $this->info("Simulating dispute webhook for booking: {$booking->booking_id}");

        // Create a mock dispute webhook payload
        $mockDisputeData = [
            'id' => 'dp_test_' . uniqid(),
            'charge' => 'ch_test_' . uniqid(),
            'amount' => (float)$booking->total_price * 100,
            'currency' => 'gbp',
            'reason' => 'unrecognized',
            'status' => 'needs_response',
            'created' => time(),
            'evidence_due_by' => time() + (7 * 24 * 60 * 60), // 7 days from now
            'evidence_details' => null
        ];

        // Mock the webhook service to find this booking
        $this->info("📡 Simulating charge.dispute.created webhook...");
        
        // Note: In real testing, you'd need to modify the findBookingByChargeId method
        // to handle test bookings, or use Stripe's test mode with actual test charges
        
        $this->table(
            ['Webhook Property', 'Value'],
            [
                ['Event Type', 'charge.dispute.created'],
                ['Dispute ID', $mockDisputeData['id']],
                ['Charge ID', $mockDisputeData['charge']],
                ['Amount', '£' . number_format($mockDisputeData['amount'] / 100, 2)],
                ['Reason', $mockDisputeData['reason']],
                ['Status', $mockDisputeData['status']],
                ['Evidence Due', date('Y-m-d H:i:s', $mockDisputeData['evidence_due_by'])],
            ]
        );

        $this->newLine();
        $this->warn("⚠️  This is a simulation. For real webhook testing:");
        $this->info("1. Use Stripe CLI: stripe listen --forward-to localhost:8000/webhooks/stripe");
        $this->info("2. Create test payments in Stripe Dashboard");
        $this->info("3. Create disputes on test payments");
        $this->info("4. Or use the dispute:test command for email testing");

        return 0;
    }
}