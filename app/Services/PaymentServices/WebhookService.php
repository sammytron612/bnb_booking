<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Exception;

class WebhookService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Handle checkout session completed webhook
     */
    public function handleCheckoutSessionCompleted($session): void
    {
        $bookingId = $session['metadata']['booking_id'] ?? null;

        if ($bookingId) {
            $booking = Booking::find($bookingId);

            if ($booking) {
                // Use database transaction to prevent race conditions
                DB::transaction(function () use ($booking, $session) {
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
                        'stripe_metadata' => $session['metadata'] ?? [],
                        'pay_method' => 'stripe',
                    ];

                    // Enhanced email validation and update if provided in session
                    if (!empty($session['customer_email'])) {
                        $newEmail = filter_var($session['customer_email'], FILTER_VALIDATE_EMAIL);
                        if ($newEmail && $this->paymentService->validateEmailChange($booking->email, $newEmail)) {
                            $updateData['email'] = $newEmail;
                            Log::info('Email updated during payment webhook', [
                                'booking_id' => $booking->id,
                                'old_email' => $booking->email,
                                'new_email' => $newEmail
                            ]);
                        }
                    }

                    // Update booking
                    $booking->update($updateData);

                    // Send confirmation emails only if this is a new payment
                    if (!$wasAlreadyPaid) {
                        try {
                            // Mark emails as sent first to prevent duplicates
                            $booking->update(['confirmation_email_sent' => now()]);

                            // Send confirmation email to guest
                            Mail::to($booking->email)->send(new BookingConfirmation($booking));

                            // Send notification to property owner
                            if (config('mail.owner_email')) {
                                Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                            }

                            Log::info('Confirmation emails sent successfully', [
                                'booking_id' => $booking->id,
                                'guest_email' => $booking->email
                            ]);

                        } catch (Exception $e) {
                            Log::error('Failed to send confirmation emails: ' . $e->getMessage(), [
                                'booking_id' => $booking->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                });

                Log::info('Webhook: Booking payment processed successfully', [
                    'booking_id' => $bookingId,
                    'session_id' => $session['id'] ?? 'unknown'
                ]);
            } else {
                Log::error('Webhook: Booking not found for session', [
                    'booking_id' => $bookingId,
                    'session_id' => $session['id'] ?? 'unknown'
                ]);
            }
        } else {
            Log::warning('Webhook: No booking_id in session metadata', [
                'session_id' => $session['id'] ?? 'unknown'
            ]);
        }
    }

    /**
     * Handle payment intent creation
     */
    public function handlePaymentIntentCreated($paymentIntent): void
    {
        Log::info('Payment intent created', [
            'payment_intent_id' => $paymentIntent['id'],
            'amount' => $paymentIntent['amount'],
            'currency' => $paymentIntent['currency']
        ]);
    }

    /**
     * Handle successful payment intent
     */
    public function handlePaymentIntentSucceeded($paymentIntent): void
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent['id'],
            'amount' => $paymentIntent['amount'],
            'currency' => $paymentIntent['currency']
        ]);
    }

    /**
     * Handle failed payment intent with enhanced error handling
     */
    public function handlePaymentIntentFailed($paymentIntent): void
    {
        $error = $paymentIntent['last_payment_error']['message'] ?? 'Unknown payment error';

        Log::error('Payment intent failed', [
            'payment_intent_id' => $paymentIntent['id'],
            'error' => $error,
            'amount' => $paymentIntent['amount'],
            'currency' => $paymentIntent['currency']
        ]);

        // Try to find and update the associated booking
        $booking = Booking::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
        if ($booking) {
            $booking->update([
                'status' => 'payment_failed',
                'notes' => ($booking->notes ?? '') . "\nPayment failed: " . $error
            ]);

            Log::info('Updated booking status for failed payment', [
                'booking_id' => $booking->id,
                'payment_intent_id' => $paymentIntent['id']
            ]);
        }
    }

    /**
     * Handle charge updates
     */
    public function handleChargeUpdated($charge): void
    {
        Log::info('Charge updated', [
            'charge_id' => $charge['id'],
            'status' => $charge['status'],
            'amount' => $charge['amount'],
            'payment_intent' => $charge['payment_intent']
        ]);
    }

    /**
     * Handle successful charges
     */
    public function handleChargeSucceeded($charge): void
    {
        Log::info('Charge succeeded', [
            'charge_id' => $charge['id'],
            'amount' => $charge['amount'],
            'currency' => $charge['currency'],
            'payment_intent' => $charge['payment_intent']
        ]);
    }
}
