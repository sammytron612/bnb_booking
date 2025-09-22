<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
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
        // Stripe API key will be set per-method for better security
    }

    /**
     * Validate that the request has proper access to the booking
     */
    private function validateBookingAccess(Request $request, Booking $booking, bool $requireSignature = true): bool
    {
        // Check signed URL signature only if required (checkout needs it, success/cancel don't)
        if ($requireSignature && !$request->hasValidSignature()) {
            Log::warning('Invalid signature for booking access', [
                'booking_id' => $booking->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return false;
        }

        // Additional basic validation - booking must exist and be recent
        if (!$booking || $booking->created_at < now()->subHours(48)) {
            Log::warning('Booking access validation failed - booking too old or missing', [
                'booking_id' => $booking?->id,
                'ip' => $request->ip()
            ]);
            return false;
        }

        // For success/cancel routes without signatures, require valid Stripe session ID
        if (!$requireSignature) {
            $sessionId = $request->get('session_id');

            // Must have session_id parameter that matches the booking's Stripe session
            if (!$sessionId || !$booking->stripe_session_id || $sessionId !== $booking->stripe_session_id) {
                Log::warning('Invalid or missing session_id for payment page access', [
                    'booking_id' => $booking->id,
                    'ip' => $request->ip(),
                    'provided_session_id' => $sessionId,
                    'expected_session_id' => $booking->stripe_session_id,
                    'user_agent' => $request->userAgent()
                ]);
                return false;
            }

            // Optional: Verify session ID format (Stripe session IDs start with 'cs_')
            if (!str_starts_with($sessionId, 'cs_')) {
                Log::warning('Invalid session_id format for payment page access', [
                    'booking_id' => $booking->id,
                    'ip' => $request->ip(),
                    'provided_session_id' => $sessionId
                ]);
                return false;
            }
        }

        return true;
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
    private function verifyStripePayment($session, Booking $booking): bool
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
    private function validateEmailChange(string $originalEmail, string $newEmail): bool
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
    public function createCheckoutSession(Request $request, Booking $booking)
    {
        // Validate booking access (requires signature for checkout)
        if (!$this->validateBookingAccess($request, $booking, true)) {
            return redirect()->route('home')->with('error', 'Invalid or expired booking link.');
        }

        $this->setStripeKey();

        try {
            // Enhanced validation - ensure booking is in valid state
            if ($booking->is_paid) {
                return redirect()->route('home')->with('error', 'This booking has already been paid.');
            }

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
                'cancel_url' => route('payment.cancel', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
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
        // Validate booking access (no signature required for Stripe redirects)
        if (!$this->validateBookingAccess($request, $booking, false)) {
            return redirect()->route('home')->with('error', 'Invalid or expired payment link.');
        }

        $this->setStripeKey();

        $sessionId = $request->get('session_id');

        // Enhanced security check: Ensure session_id matches and verify payment details
        if (!$sessionId || $booking->stripe_session_id !== $sessionId) {
            Log::warning('Payment success accessed with invalid session', [
                'booking_id' => $booking->id,
                'provided_session_id' => $sessionId,
                'stored_session_id' => $booking->stripe_session_id,
                'ip' => $request->ip()
            ]);
            return redirect()->route('home')->with('error', 'Invalid payment session.');
        }

        try {
            $session = Session::retrieve($sessionId);

            // Enhanced payment verification
            if (!$this->verifyStripePayment($session, $booking)) {
                Log::warning('Payment verification failed', [
                    'booking_id' => $booking->id,
                    'session_id' => $sessionId,
                    'ip' => $request->ip()
                ]);
                return redirect()->route('home')->with('error', 'Payment verification failed.');
            }

            if ($session->payment_status === 'paid') {
                $updateData = [
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'stripe_metadata' => $session->metadata->toArray(),
                    'pay_method' => 'stripe',
                ];

                // Update email if customer provided one in Stripe checkout - with validation
                if (!empty($session->customer_email)) {
                    $newEmail = filter_var($session->customer_email, FILTER_VALIDATE_EMAIL);
                    if ($newEmail && $this->validateEmailChange($booking->email, $newEmail)) {
                        $updateData['email'] = $newEmail;
                        Log::info('Email updated for booking via success page', [
                            'booking_id' => $booking->id,
                            'old_email' => $booking->email,
                            'new_email' => $newEmail
                        ]);
                    }
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
        // Validate booking access (no signature required for Stripe redirects)
        if (!$this->validateBookingAccess($request, $booking, false)) {
            return redirect()->route('home')->with('error', 'Invalid or expired booking link.');
        }

        // Additional security: Check if booking exists and is in valid state for cancel
        if (!$booking || $booking->is_paid) {
            return redirect()->route('home')->with('error', 'Invalid payment session or booking already completed.');
        }

        // Generate signed URL for retry payment (valid for 24 hours)
        $retryPaymentUrl = URL::temporarySignedRoute('payment.checkout', now()->addHours(24), ['booking' => $booking->id]);

        return view('payment.cancel', compact('booking', 'retryPaymentUrl'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request): Response
    {
        $this->setStripeKey();

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        // Enhanced logging for security monitoring
        Log::info('Webhook received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_length' => strlen($payload)
        ]);

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

                    // Update email if customer provided one in Stripe checkout - with validation
                    if (!empty($session['customer_email'])) {
                        // Validate email format and ensure it's not too different from original
                        $newEmail = filter_var($session['customer_email'], FILTER_VALIDATE_EMAIL);
                        if ($newEmail && $this->validateEmailChange($booking->email, $newEmail)) {
                            $updateData['email'] = $newEmail;
                            Log::info('Email updated for booking via webhook', [
                                'booking_id' => $booking->id,
                                'old_email' => $booking->email,
                                'new_email' => $newEmail
                            ]);
                        } else {
                            Log::warning('Suspicious email change attempt blocked', [
                                'booking_id' => $booking->id,
                                'original_email' => $booking->email,
                                'attempted_email' => $session['customer_email']
                            ]);
                        }
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
