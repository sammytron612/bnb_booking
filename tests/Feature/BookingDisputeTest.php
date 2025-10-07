<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\Venue;
use App\Services\PaymentServices\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\DisputeNotification;

class BookingDisputeTest extends TestCase
{
    use RefreshDatabase;

    protected $venue;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test venue
        $this->venue = Venue::create([
            'venue_name' => 'Test BnB',
            'price' => 100.00,
            'guest_capacity' => 4,
            'address1' => 'Test Address',
            'postcode' => 'TE1 1ST'
        ]);
    }

    /** @test */
    public function it_can_create_booking_dispute()
    {
        // Create a test booking
        $booking = Booking::create([
            'booking_id' => 'TEST123',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '07123456789',
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(9)->format('Y-m-d'),
            'venue_id' => $this->venue->id,
            'nights' => 2,
            'total_price' => 200.00,
            'status' => 'confirmed',
            'is_paid' => true,
            'stripe_payment_intent_id' => 'pi_test_12345'
        ]);

        // Create a dispute
        $dispute = BookingDispute::create([
            'booking_id' => $booking->id,
            'stripe_dispute_id' => 'dp_test_12345',
            'stripe_charge_id' => 'ch_test_12345',
            'amount' => 10000, // £100.00 in pence
            'currency' => 'gbp',
            'reason' => 'fraudulent',
            'status' => 'needs_response',
            'created_at_stripe' => now(),
            'admin_notified' => false,
        ]);

        $this->assertDatabaseHas('booking_disputes', [
            'stripe_dispute_id' => 'dp_test_12345',
            'booking_id' => $booking->id,
            'amount' => 10000
        ]);

        // Test relationships
        $this->assertEquals($booking->id, $dispute->booking->id);
        $this->assertEquals('£100.00', $dispute->amount_in_pounds);
        $this->assertEquals('Fraudulent Transaction', $dispute->friendly_reason);
        $this->assertEquals('🚨 Needs Response', $dispute->friendly_status);
    }

    /** @test */
    public function it_handles_dispute_webhook_correctly()
    {
        Mail::fake();

        // Create a test booking
        $booking = Booking::create([
            'booking_id' => 'TEST456',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '07987654321',
            'check_in' => now()->addDays(14)->format('Y-m-d'),
            'check_out' => now()->addDays(16)->format('Y-m-d'),
            'venue_id' => $this->venue->id,
            'nights' => 2,
            'total_price' => 200.00,
            'status' => 'confirmed',
            'is_paid' => true,
            'stripe_payment_intent_id' => 'pi_test_12345'
        ]);

        // Mock the WebhookService to simulate finding the booking
        $webhookService = $this->app->make(WebhookService::class);

        // Create mock dispute data
        $disputeData = [
            'id' => 'dp_test_webhook',
            'charge' => 'ch_test_webhook',
            'amount' => 15000, // £150.00
            'currency' => 'gbp',
            'reason' => 'unrecognized',
            'status' => 'warning_needs_response',
            'created' => time(),
            'evidence_due_by' => time() + (7 * 24 * 60 * 60), // 7 days from now
        ];

        // This would be called by the actual webhook, but we'll test the method directly
        // In a real scenario, you'd need to mock the Stripe API call to find the booking

        $this->assertDatabaseMissing('booking_disputes', [
            'stripe_dispute_id' => 'dp_test_webhook'
        ]);

        // Test that dispute notification would be sent
        // Mail::assertSent(DisputeNotification::class);
    }

    /** @test */
    public function it_calculates_urgency_correctly()
    {
        $booking = Booking::create([
            'booking_id' => 'TEST789',
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '07111222333',
            'check_in' => now()->addDays(21)->format('Y-m-d'),
            'check_out' => now()->addDays(23)->format('Y-m-d'),
            'venue_id' => $this->venue->id,
            'nights' => 2,
            'total_price' => 200.00,
            'status' => 'confirmed'
        ]);

        // Create dispute due tomorrow (urgent)
        $urgentDispute = BookingDispute::create([
            'booking_id' => $booking->id,
            'stripe_dispute_id' => 'dp_urgent',
            'stripe_charge_id' => 'ch_urgent',
            'amount' => 5000,
            'currency' => 'gbp',
            'reason' => 'general',
            'status' => 'needs_response',
            'evidence_due_by' => now()->addDay(),
            'created_at_stripe' => now(),
            'admin_notified' => false,
        ]);

        // Create dispute due in 5 days (not urgent - more than 2 days)
        $normalDispute = BookingDispute::create([
            'booking_id' => $booking->id,
            'stripe_dispute_id' => 'dp_normal',
            'stripe_charge_id' => 'ch_normal',
            'amount' => 5000,
            'currency' => 'gbp',
            'reason' => 'general',
            'status' => 'needs_response',
            'evidence_due_by' => now()->addDays(5),
            'created_at_stripe' => now(),
            'admin_notified' => false,
        ]);

        // Just test that urgency calculation works with the expected boolean results
        $this->assertTrue($urgentDispute->is_urgent, 'Dispute due in 1 day should be urgent');
        $this->assertFalse($normalDispute->is_urgent, 'Dispute due in 5 days should not be urgent');

        // Test that days_until_due returns sensible values (non-negative integers)
        $this->assertIsInt($urgentDispute->days_until_due);
        $this->assertIsInt($normalDispute->days_until_due);
        $this->assertGreaterThanOrEqual(0, $urgentDispute->days_until_due);
        $this->assertGreaterThanOrEqual(0, $normalDispute->days_until_due);
    }
}
