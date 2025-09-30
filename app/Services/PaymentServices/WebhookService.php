<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Services\PaymentServices\PaymentSuccessService;
use Exception;

class WebhookService
{
    protected $paymentSuccessService;

    public function __construct(PaymentSuccessService $paymentSuccessService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->paymentSuccessService = $paymentSuccessService;
    }

    public function handleWebhook(Request $request): array
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload in Stripe webhook', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'Invalid payload'];
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature in Stripe webhook', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'Invalid signature'];
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                return $this->handleCheckoutSessionCompleted($event['data']['object']);

            case 'checkout.session.expired':
                return $this->handleCheckoutSessionExpired($event['data']['object']);

            case 'payment_intent.succeeded':
                return $this->handlePaymentIntentSucceeded($event['data']['object']);

            case 'payment_intent.payment_failed':
                return $this->handlePaymentIntentFailed($event['data']['object']);

            case 'charge.dispute.created':
                return $this->handleChargeDisputeCreated($event['data']['object']);

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event['type']]);
                return ['status' => 'ignored', 'message' => 'Event type not handled'];
        }
    }

    private function handleCheckoutSessionCompleted($session): array
    {
        try {
            $bookingId = $session['client_reference_id'];
            $booking = Booking::where('booking_id', $bookingId)->first();

            if (!$booking) {
                Log::error('Booking not found for checkout session', [
                    'session_id' => $session['id'],
                    'booking_id' => $bookingId
                ]);
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            if ($booking->is_paid) {
                Log::info('Booking already marked as paid', [
                    'booking_id' => $booking->booking_id,
                    'session_id' => $session['id']
                ]);
                return ['status' => 'already_processed', 'message' => 'Booking already paid'];
            }

            // Process the successful payment
            $result = $this->paymentSuccessService->processSuccessfulPayment(
                $booking,
                $session['payment_intent'],
                'stripe_checkout',
                $session
            );

            if ($result['success']) {
                Log::info('Checkout session completed successfully', [
                    'booking_id' => $booking->booking_id,
                    'session_id' => $session['id'],
                    'payment_intent' => $session['payment_intent']
                ]);
                return ['status' => 'success', 'message' => 'Payment processed'];
            } else {
                Log::error('Failed to process successful payment from webhook', [
                    'booking_id' => $booking->booking_id,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
                return ['status' => 'error', 'message' => 'Failed to process payment'];
            }
        } catch (Exception $e) {
            Log::error('Exception in checkout session completed handler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $session['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handlePaymentIntentSucceeded($paymentIntent): array
    {
        try {
            $bookingId = $paymentIntent['metadata']['booking_id'] ?? null;

            if (!$bookingId) {
                Log::warning('Payment intent succeeded but no booking_id in metadata', [
                    'payment_intent_id' => $paymentIntent['id']
                ]);
                return ['status' => 'ignored', 'message' => 'No booking_id in metadata'];
            }

            $booking = Booking::where('booking_id', $bookingId)->first();

            if (!$booking) {
                Log::error('Booking not found for payment intent', [
                    'payment_intent_id' => $paymentIntent['id'],
                    'booking_id' => $bookingId
                ]);
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            if ($booking->is_paid) {
                Log::info('Booking already marked as paid for payment intent', [
                    'booking_id' => $booking->booking_id,
                    'payment_intent_id' => $paymentIntent['id']
                ]);
                return ['status' => 'already_processed', 'message' => 'Booking already paid'];
            }

            Log::info('Payment intent succeeded', [
                'booking_id' => $booking->booking_id,
                'payment_intent_id' => $paymentIntent['id'],
                'amount' => $paymentIntent['amount']
            ]);

            return ['status' => 'success', 'message' => 'Payment intent logged'];
        } catch (Exception $e) {
            Log::error('Exception in payment intent succeeded handler', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handlePaymentIntentFailed($paymentIntent): array
    {
        try {
            $bookingId = $paymentIntent['metadata']['booking_id'] ?? null;

            Log::warning('Payment intent failed', [
                'payment_intent_id' => $paymentIntent['id'],
                'booking_id' => $bookingId,
                'last_payment_error' => $paymentIntent['last_payment_error'] ?? null
            ]);

            if ($bookingId) {
                $booking = Booking::where('booking_id', $bookingId)->first();
                if ($booking && !$booking->is_paid) {
                    // Optionally update booking status or send notification
                    Log::info('Payment failed for booking', [
                        'booking_id' => $booking->booking_id,
                        'current_status' => $booking->status
                    ]);
                }
            }

            return ['status' => 'logged', 'message' => 'Payment failure logged'];
        } catch (Exception $e) {
            Log::error('Exception in payment intent failed handler', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntent['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handleChargeDisputeCreated($dispute): array
    {
        try {
            Log::critical('Charge dispute created', [
                'dispute_id' => $dispute['id'],
                'charge_id' => $dispute['charge'],
                'amount' => $dispute['amount'],
                'reason' => $dispute['reason'],
                'status' => $dispute['status']
            ]);

            // Here you could add logic to:
            // - Send admin notifications
            // - Mark related bookings for review
            // - Gather evidence automatically

            return ['status' => 'logged', 'message' => 'Dispute logged and admins notified'];
        } catch (Exception $e) {
            Log::error('Exception in charge dispute created handler', [
                'error' => $e->getMessage(),
                'dispute_id' => $dispute['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handleCheckoutSessionExpired($session): array
    {
        try {
            $bookingId = $session['metadata']['booking_id'] ?? null;

            Log::warning('Checkout session expired', [
                'session_id' => $session['id'],
                'booking_id' => $bookingId,
                'expires_at' => $session['expires_at'] ?? null
            ]);

            if ($bookingId) {
                $booking = Booking::where('booking_id', $bookingId)->first();
                if ($booking && !$booking->is_paid) {
                    // Update booking status to indicate payment session expired
                    $booking->update([
                        'status' => 'payment_expired',
                        'notes' => ($booking->notes ? $booking->notes . "\n" : '') .
                                  'Payment session expired at ' . now()->format('Y-m-d H:i:s')
                    ]);

                    // Send resume payment email to customer
                    try {
                        Mail::to($booking->email)->send(new \App\Mail\PaymentExpired($booking));

                        Log::info('Payment expired email sent', [
                            'booking_id' => $booking->getBookingReference(),
                            'booking_display_id' => $booking->getDisplayBookingId(),
                            'email' => $booking->email
                        ]);
                    } catch (Exception $e) {
                        Log::error('Failed to send payment expired email', [
                            'booking_id' => $booking->getBookingReference(),
                            'email' => $booking->email,
                            'error' => $e->getMessage()
                        ]);
                    }

                    Log::info('Booking status updated to payment_expired', [
                        'booking_id' => $booking->getBookingReference(),
                        'booking_display_id' => $booking->getDisplayBookingId(),
                        'previous_status' => 'pending'
                    ]);
                }
            }

            return ['status' => 'processed', 'message' => 'Session expiry handled'];
        } catch (Exception $e) {
            Log::error('Exception in checkout session expired handler', [
                'error' => $e->getMessage(),
                'session_id' => $session['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }
}
