<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Exception;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Booking $booking): ?Session
    {
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => 'Booking at ' . $booking->venue->venue_name,
                            'description' => sprintf(
                                'Stay from %s to %s (%d nights)',
                                $booking->check_in,
                                $booking->check_out,
                                $booking->nights
                            ),
                        ],
                        'unit_amount' => (int)($booking->total_price * 100), // Convert to pence
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'client_reference_id' => $booking->booking_id,
                'customer_email' => $booking->email,
                'success_url' => URL::temporarySignedRoute(
                    'payment.success',
                    now()->addHours(1),
                    ['booking' => $booking->id]
                ),
                'cancel_url' => URL::temporarySignedRoute(
                    'payment.cancel',
                    now()->addHours(1),
                    ['booking' => $booking->id]
                ),
                'metadata' => [
                    'booking_id' => $booking->booking_id,
                    'venue_name' => $booking->venue->venue_name,
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'booking_id' => $booking->booking_id,
                        'venue_id' => $booking->venue_id,
                        'customer_name' => $booking->name,
                        'customer_email' => $booking->email,
                    ],
                ],
            ]);

            Log::info('Stripe checkout session created', [
                'booking_id' => $booking->booking_id,
                'session_id' => $session->id,
                'amount' => $booking->total_price
            ]);

            return $session;
        } catch (Exception $e) {
            Log::error('Failed to create Stripe checkout session', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function retrievePaymentIntent(string $paymentIntentId): ?PaymentIntent
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (Exception $e) {
            Log::error('Failed to retrieve payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function retrieveCheckoutSession(string $sessionId): ?Session
    {
        try {
            return Session::retrieve($sessionId);
        } catch (Exception $e) {
            Log::error('Failed to retrieve checkout session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function processRefund(string $paymentIntentId, ?int $amount = null): bool
    {
        try {
            $refund = \Stripe\Refund::create([
                'payment_intent' => $paymentIntentId,
                'amount' => $amount, // null for full refund
            ]);

            Log::info('Refund processed successfully', [
                'payment_intent_id' => $paymentIntentId,
                'refund_id' => $refund->id,
                'amount' => $amount
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to process refund', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
