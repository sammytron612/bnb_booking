<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        $this->setStripeKey();
    }

    /**
     * Set Stripe API key securely for individual operations
     */
    private function setStripeKey(): void
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    /**
     * Enhanced Stripe payment verification
     */
    public function verifyStripePayment($session, Booking $booking): bool
    {
        // Verify payment status
        if ($session->payment_status !== 'paid') {
            return false;
        }

        // Verify booking ID in metadata
        if (!isset($session->metadata['booking_id']) || $session->metadata['booking_id'] != $booking->id) {
            return false;
        }

        // Verify payment amount matches booking total
        $expectedAmount = (int) ($booking->total_price * 100); // Convert to cents
        if ($session->amount_total !== $expectedAmount) {
            return false;
        }

        // Verify currency
        if ($session->currency !== 'gbp') {
            return false;
        }

        return true;
    }

    /**
     * Validate email change to prevent email hijacking
     */
    public function validateEmailChange(string $originalEmail, string $newEmail): bool
    {
        // If emails are the same, allow
        if ($originalEmail === $newEmail) {
            return true;
        }

        // Extract domains
        $originalDomain = substr(strrchr($originalEmail, "@"), 1);
        $newDomain = substr(strrchr($newEmail, "@"), 1);

        // Allow if same domain (user might have corrected their email)
        if ($originalDomain === $newDomain) {
            return true;
        }

        // For different domains, be more restrictive
        // Only allow common email providers to prevent domain hijacking
        $trustedDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];

        return in_array($newDomain, $trustedDomains) && in_array($originalDomain, $trustedDomains);
    }

    /**
     * Create a Stripe checkout session for a booking
     */
    public function createCheckoutSession(Booking $booking): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => $booking->venue->venue_name . ' Booking - ' . $booking->getDisplayBookingId(),
                        'description' => 'Booking from ' . Carbon::parse($booking->check_in)->format('M j, Y') . ' to ' . Carbon::parse($booking->check_out)->format('M j, Y'),
                    ],
                    'unit_amount' => (int) ($booking->total_price * 100), // Convert to pence
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $booking->email, // Pre-fill email from booking
            'success_url' => route('payment.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->getDisplayBookingId(),
                'venue' => $booking->venue->venue_name,
                'guest_name' => $booking->name,
            ],
        ]);
    }

    /**
     * Update booking with Stripe session details
     */
    public function updateBookingWithSession(Booking $booking, Session $session): void
    {
        $booking->update([
            'stripe_session_id' => $session->id,
            'stripe_amount' => $booking->total_price,
            'stripe_currency' => 'gbp',
        ]);
    }
}
