<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Exception;

class PaymentSuccessService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Validate session ID matches booking
     */
    public function validateSessionId(?string $sessionId, Booking $booking): bool
    {
        if (!$sessionId || !$booking->stripe_session_id) {
            return false;
        }

        return $sessionId === $booking->stripe_session_id;
    }

    /**
     * Process payment success flow
     */
    public function processPaymentSuccess(Session $session, Booking $booking): bool
    {
        // Enhanced payment verification
        if (!$this->paymentService->verifyStripePayment($session, $booking)) {
            Log::warning('Payment verification failed', [
                'booking_id' => $booking->id,
                'session_id' => $session->id,
            ]);
            return false;
        }

        if ($session->payment_status === 'paid') {
            $this->updateBookingStatus($session, $booking);
            $this->sendConfirmationEmailsIfNeeded($booking);
            return true;
        }

        return false;
    }

    /**
     * Update booking status with payment information
     */
    private function updateBookingStatus(Session $session, Booking $booking): void
    {
        $updateData = [
            'is_paid' => true,
            'status' => 'confirmed',
            'payment_completed_at' => now(),
            'stripe_payment_intent_id' => $session->payment_intent,
            'stripe_metadata' => $session->metadata->toArray(),
            'pay_method' => 'stripe',
        ];

        // Enhanced email validation and update if provided
        if (!empty($session->customer_email)) {
            $newEmail = filter_var($session->customer_email, FILTER_VALIDATE_EMAIL);
            if ($newEmail && $this->paymentService->validateEmailChange($booking->email, $newEmail)) {
                $updateData['email'] = $newEmail;
                Log::info('Email updated via payment success', [
                    'booking_id' => $booking->id,
                    'old_email' => $booking->email,
                    'new_email' => $newEmail
                ]);
            }
        }

        $booking->update($updateData);
    }

    /**
     * Send confirmation emails if not already sent (backup mechanism)
     */
    private function sendConfirmationEmailsIfNeeded(Booking $booking): void
    {
        // Check if emails were already sent (webhook usually handles this)
        if ($booking->confirmation_email_sent) {
            return;
        }

        DB::transaction(function () use ($booking) {
            // Re-fetch booking with lock to prevent race conditions
            $booking = Booking::where('id', $booking->id)->lockForUpdate()->first();

            // Double-check if emails were sent while we were waiting for lock
            if (!$booking->confirmation_email_sent) {
                try {
                    // Mark emails as sent first to prevent duplicates
                    $booking->update(['confirmation_email_sent' => now()]);

                    // Send confirmation email to guest
                    Mail::to($booking->email)->send(new BookingConfirmation($booking));

                    // Send notification to property owner
                    if (config('mail.owner_email')) {
                        Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                    }

                    Log::info('Backup confirmation emails sent from success page', [
                        'booking_id' => $booking->id,
                        'guest_email' => $booking->email
                    ]);

                } catch (Exception $e) {
                    Log::error('Failed to send backup confirmation emails: ' . $e->getMessage(), [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }
}
