<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Exception;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    /**
     * Create a Stripe checkout session for a booking
     */
    public function createCheckoutSession(Request $request, Booking $booking)
    {
        try {
            $session = Session::create([
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
                'cancel_url' => route('payment.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->getDisplayBookingId(),
                    'venue' => $booking->venue->venue_name,
                    'guest_name' => $booking->name,
                ],
            ]);            // Store session ID in booking
            $booking->update([
                'stripe_session_id' => $session->id,
                'stripe_amount' => $booking->total_price,
                'stripe_currency' => 'gbp',
            ]);

            // If this is an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'session_id' => $session->id,
                    'checkout_url' => $session->url,
                ]);
            }

            // Otherwise redirect to Stripe checkout
            return redirect($session->url);

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Unable to create payment session. Please try again.');
        }
    }

    /**
     * Handle successful payment
     */
    public function paymentSuccess(Request $request, Booking $booking)
    {
        $sessionId = $request->get('session_id');

        // Security check: Ensure session_id matches the booking's stored session_id
        if (!$sessionId || $booking->stripe_session_id !== $sessionId) {
            // If no session_id or it doesn't match, redirect to home with error
            return redirect()->route('home')->with('error', 'Invalid payment session.');
        }

        try {
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $updateData = [
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'stripe_metadata' => $session->metadata->toArray(),
                    'pay_method' => 'stripe',
                ];

                // Update email if customer provided one in Stripe checkout
                if (!empty($session->customer_email)) {
                    $updateData['email'] = $session->customer_email;
                }

                $booking->update($updateData);

                // Check if webhook has already processed this booking and sent emails
                // If not, send emails from here as backup (for cases where webhooks fail)
                if (!$booking->confirmation_email_sent) {
                    \DB::transaction(function () use ($booking) {
                        // Re-fetch booking to check current state
                        $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                        // Double-check that emails haven't been sent by webhook in the meantime
                        if (!$booking->confirmation_email_sent) {
                            try {
                                // Mark emails as sent to prevent duplicate sending
                                $booking->update(['confirmation_email_sent' => now()]);

                                // Send confirmation email to customer
                                Mail::to($booking->email)->send(new BookingConfirmation($booking));

                                // Send notification to owner
                                if (config('mail.owner_email')) {
                                    Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                                }

                                Log::info('Confirmation emails sent from success page (webhook backup)', [
                                    'booking_id' => $booking->id,
                                    'reason' => 'webhook_not_received'
                                ]);
                            } catch (Exception $e) {
                                Log::error('Failed to send backup confirmation email from success page: ' . $e->getMessage(), [
                                    'booking_id' => $booking->id
                                ]);
                            }
                        } else {
                            Log::info('Emails already sent by webhook, skipped backup sending', [
                                'booking_id' => $booking->id
                            ]);
                        }
                    });
                } else {
                    Log::info('Booking updated from success page - emails already sent', [
                        'booking_id' => $booking->id
                    ]);
                }
            }
        } catch (Exception $e) {
            // Log error but still show success page
            Log::error('Failed to update booking after successful payment: ' . $e->getMessage());
        }

        return view('payment.success', compact('booking'));
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Request $request, Booking $booking)
    {
        // Validate that this is a legitimate cancel request
        // Either from Stripe (no additional validation needed) or direct access (basic security check)

        // Additional security: Check if booking exists and is in valid state for cancel
        if (!$booking || $booking->is_paid) {
            return redirect()->route('home')->with('error', 'Invalid payment session or booking already completed.');
        }

        // Generate URL for retry payment
        $retryPaymentUrl = route('payment.checkout', ['booking' => $booking->id]);

        return view('payment.cancel', compact('booking', 'retryPaymentUrl'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            Log::info('Stripe webhook received', ['type' => $event['type']]);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response('', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;
            case 'payment_intent.created':
                $this->handlePaymentIntentCreated($event['data']['object']);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;
            case 'charge.updated':
                $this->handleChargeUpdated($event['data']['object']);
                break;
            case 'charge.succeeded':
                $this->handleChargeSucceeded($event['data']['object']);
                break;
            default:
                Log::info('Unhandled webhook event type: ' . $event['type']);
        }

        return response('', 200);
    }

    /**
     * Handle checkout session completed webhook
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $bookingId = $session['metadata']['booking_id'] ?? null;

        if ($bookingId) {
            $booking = Booking::find($bookingId);

            if ($booking) {
                // Use database transaction to prevent race conditions
                \DB::transaction(function () use ($booking, $session) {
                    // Re-fetch booking with lock to prevent race conditions
                    $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();

                    // Check if emails have already been sent
                    if ($booking->confirmation_email_sent) {
                        Log::info('Emails already sent for this booking', [
                            'booking_id' => $booking->id,
                            'email_sent_at' => $booking->confirmation_email_sent
                        ]);
                        return;
                    }

                    // Check if booking is already paid to prevent duplicate emails
                    $wasAlreadyPaid = $booking->is_paid;

                    $updateData = [
                        'is_paid' => true,
                        'status' => 'confirmed',
                        'payment_completed_at' => now(),
                        'stripe_payment_intent_id' => $session['payment_intent'],
                        'stripe_metadata' => $session['metadata'],
                        'pay_method' => 'stripe',
                    ];

                    // Update email if customer provided one in Stripe checkout
                    if (!empty($session['customer_email'])) {
                        $updateData['email'] = $session['customer_email'];
                    }

                    // Send emails if they haven't been sent yet (regardless of payment status)
                    // This fixes race condition where success page marks as paid before webhook runs
                    if (!$booking->confirmation_email_sent) {
                        try {
                            // Mark emails as sent FIRST to prevent other webhooks from sending
                            $updateData['confirmation_email_sent'] = now();
                            $booking->update($updateData);

                            // Send confirmation email to CUSTOMER only
                            Mail::to($booking->email)->send(new BookingConfirmation($booking));

                            // Send new booking notification to OWNER only (not customer)
                            if (config('mail.owner_email')) {
                                Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                            }

                            Log::info('Booking confirmed - customer email sent, owner notified', [
                                'booking_id' => $booking->id,
                                'was_already_paid' => $wasAlreadyPaid,
                                'sent_via' => 'webhook'
                            ]);
                        } catch (Exception $e) {
                            Log::error('Failed to send confirmation email: ' . $e->getMessage(), [
                                'booking_id' => $booking->id
                            ]);
                        }
                    } else {
                        // Just update booking data without sending emails
                        $booking->update($updateData);
                        Log::info('Emails already sent for this booking, webhook updated booking only', [
                            'booking_id' => $booking->id,
                            'email_sent_at' => $booking->confirmation_email_sent
                        ]);
                    }
                });
            } else {
                Log::error('Booking not found for webhook', ['booking_id' => $bookingId]);
            }
        } else {
            Log::error('No booking_id in webhook metadata');
        }
    }    /**
     * Handle payment intent succeeded webhook
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Additional handling if needed
        Log::info('Payment intent succeeded', ['payment_intent_id' => $paymentIntent['id']]);
    }

    /**
     * Handle payment intent created webhook
     */
    private function handlePaymentIntentCreated($paymentIntent)
    {
        // Payment intent created - no action needed
    }

    /**
     * Handle payment intent failed webhook
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        // Find booking by payment intent ID and handle failure
        $booking = Booking::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if ($booking) {
            Log::warning('Payment failed for booking', [
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntent['id']
            ]);
        }
    }

    /**
     * Handle charge updated webhook
     */
    private function handleChargeUpdated($charge)
    {
        // If charge is succeeded, try to find and update booking
        if ($charge['status'] === 'succeeded' && !empty($charge['payment_intent'])) {
            $booking = Booking::where('stripe_payment_intent_id', $charge['payment_intent'])->first();

            if ($booking && !$booking->is_paid) {
                $booking->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                    'pay_method' => 'stripe',
                ]);

                Log::info('Booking updated via charge.updated (no emails sent)', ['booking_id' => $booking->id]);
            }
        }
    }

    /**
     * Handle charge succeeded webhook
     */
    private function handleChargeSucceeded($charge)
    {
        // Try to find booking by payment intent and update if not already paid
        if (!empty($charge['payment_intent'])) {
            $booking = Booking::where('stripe_payment_intent_id', $charge['payment_intent'])->first();

            if ($booking && !$booking->is_paid) {
                $booking->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                    'pay_method' => 'stripe',
                ]);

                Log::info('Booking updated via charge.succeeded (no emails sent)', ['booking_id' => $booking->id]);
            }
        }
    }
}
