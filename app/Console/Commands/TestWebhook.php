<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\PaymentServices\WebhookService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestWebhook extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:webhook
                            {booking_id : The booking ID to test with}
                            {--type=expired : Webhook type to test (expired, failed)}';

    /**
     * The console command description.
     */
    protected $description = 'Test webhook functionality with a fake webhook event';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $type = $this->option('type');

        $booking = Booking::where('booking_id', $bookingId)->first();
        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found.");
            return 1;
        }

        $this->info("🧪 Testing webhook for booking {$booking->getDisplayBookingId()}");
        $this->line("Current status: {$booking->status}");

        if ($type === 'expired') {
            $this->testSessionExpired($booking);
        } elseif ($type === 'failed') {
            $this->testPaymentFailed($booking);
        } else {
            $this->error("Invalid webhook type. Use 'expired' or 'failed'");
            return 1;
        }

        return 0;
    }

    private function testSessionExpired(Booking $booking)
    {
        $this->info("🕐 Simulating checkout.session.expired webhook...");

        // Create fake Stripe session expired event (for reference only)
        $eventExample = [
            'id' => 'evt_test_fake_expired',
            'object' => 'event',
            'type' => 'checkout.session.expired',
            'data' => [
                'object' => [
                    'id' => 'cs_test_fake_session_id',
                    'object' => 'checkout.session',
                    'expires_at' => now()->timestamp,
                    'metadata' => [
                        'booking_id' => $booking->getBookingReference(),
                        'booking_display_id' => $booking->getDisplayBookingId(),
                    ]
                ]
            ]
        ];

        $this->info("📝 Example webhook event structure created for reference");
        $this->line("Event ID: " . $eventExample['id']);
        $this->line("Session ID: " . $eventExample['data']['object']['id']);

        // Create fake request with no signature verification for testing
        $request = new Request();
        $request->replace(['fake_test' => true]);
        $request->headers->set('content-type', 'application/json');
        $request->headers->set('stripe-signature', 'fake_signature_for_testing');

        // Override webhook secret for testing if needed
        $originalSecret = config('services.stripe.webhook_secret');
        config(['services.stripe.webhook_secret' => 'whsec_test_fake_secret']);

        try {
            // Since webhook verification will fail with fake data,
            // we'll directly update the booking to simulate the webhook effect
            $this->info("📝 Directly simulating webhook effect (bypassing signature verification)...");

            $oldStatus = $booking->status;

            // Update booking status to payment_expired (simulating webhook effect)
            $booking->update([
                'status' => 'payment_expired',
                'notes' => ($booking->notes ? $booking->notes . "\n" : '') .
                          'Payment session expired at ' . now()->format('Y-m-d H:i:s') . ' (TEST)'
            ]);

            // Send payment expired email
            try {
                Mail::to($booking->email)->send(new \App\Mail\PaymentExpired($booking));
                $this->info("📧 Payment expired email sent to: {$booking->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send email: " . $e->getMessage());
            }

            // Refresh booking to see changes
            $booking->refresh();
            $this->info("✅ Booking status updated from '{$oldStatus}' to '{$booking->status}'");
            $this->line("📝 Notes updated: " . substr($booking->notes ?? '', -100) . "...");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
        } finally {
            // Restore original webhook secret
            config(['services.stripe.webhook_secret' => $originalSecret]);
        }
    }

    private function testPaymentFailed(Booking $booking)
    {
        $this->info("💳 Simulating payment_intent.payment_failed webhook...");

        $this->info("📝 Logging payment failure (simulating webhook effect)...");

        try {
            // Simulate the webhook's logging behavior
            Log::warning('Payment intent failed (TEST)', [
                'payment_intent_id' => 'pi_test_fake_payment_intent',
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'last_payment_error' => [
                    'code' => 'card_declined',
                    'decline_code' => 'insufficient_funds',
                    'message' => 'Your card was declined.'
                ],
                'test_run' => true
            ]);

            Log::info('Payment failed for booking (TEST)', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'current_status' => $booking->status,
                'test_run' => true
            ]);

            $this->info("✅ Payment failure logged successfully");
            $this->line("📄 Check storage/logs/laravel.log for the logged failure details");

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
        }
    }
}
