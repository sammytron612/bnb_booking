<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use App\Models\Arn;
use App\Models\BookingDispute;
use App\Mail\DisputeNotification;
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
        Stripe::setApiKey(config('services.stripe.secret_key'));
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

            case 'charge.dispute.updated':
                return $this->handleChargeDisputeUpdated($event['data']['object']);

            case 'charge.dispute.closed':
                return $this->handleChargeDisputeClosed($event['data']['object']);

            case 'charge.refunded':
                return $this->handleChargeRefunded($event['data']['object']);

            case 'refund.created':
                return $this->handleRefundCreated($event['data']['object']);

            case 'refund.updated':
                return $this->handleRefundUpdated($event['data']['object']);

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event['type']]);
                return ['status' => 'ignored', 'message' => 'Event type not handled'];
        }
    }

    private function handleCheckoutSessionCompleted($session): array
    {
        try {
            Log::info('Processing checkout.session.completed webhook', [
                'session_id' => $session['id'],
                'payment_status' => $session['payment_status'] ?? 'unknown'
            ]);

            $bookingId = $session['client_reference_id'];

            Log::info('Looking for booking', [
                'client_reference_id' => $bookingId,
                'session_id' => $session['id']
            ]);

            $booking = Booking::where('booking_id', $bookingId)->first();

            if (!$booking) {
                Log::error('Booking not found for checkout session', [
                    'session_id' => $session['id'],
                    'booking_id' => $bookingId
                ]);
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            Log::info('Booking found', [
                'booking_id' => $booking->booking_id,
                'current_status' => $booking->status,
                'is_paid' => $booking->is_paid,
                'session_id' => $session['id']
            ]);

            if ($booking->is_paid) {
                Log::info('Booking already marked as paid', [
                    'booking_id' => $booking->booking_id,
                    'session_id' => $session['id']
                ]);
                return ['status' => 'already_processed', 'message' => 'Booking already paid'];
            }

            Log::info('Processing checkout session completed for unpaid booking', [
                'booking_id' => $booking->booking_id,
                'session_id' => $session['id'],
                'current_status' => $booking->status,
                'is_paid' => $booking->is_paid
            ]);

            // Process the successful payment
            Log::info('Calling PaymentSuccessService', [
                'booking_id' => $booking->booking_id,
                'payment_intent' => $session['payment_intent']
            ]);

            $result = $this->paymentSuccessService->processSuccessfulPayment(
                $booking,
                $session['payment_intent'],
                'stripe_checkout',
                (array) $session  // Convert object to array
            );

            Log::info('PaymentSuccessService completed', [
                'booking_id' => $booking->booking_id,
                'result_success' => $result['success'] ?? false,
                'result_message' => $result['message'] ?? 'No message'
            ]);

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
                'session_id' => $session['id'] ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['status' => 'error', 'message' => 'Internal error: ' . $e->getMessage()];
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
            $lastPaymentError = $paymentIntent['last_payment_error'] ?? null;
            $declineCode = $lastPaymentError['decline_code'] ?? null;
            $errorMessage = $lastPaymentError['message'] ?? 'Payment failed';

            Log::warning('Payment intent failed', [
                'payment_intent_id' => $paymentIntent['id'],
                'booking_id' => $bookingId,
                'decline_code' => $declineCode,
                'error_message' => $errorMessage,
                'last_payment_error' => $lastPaymentError
            ]);

            if ($bookingId) {
                $booking = Booking::where('booking_id', $bookingId)->first();
                if ($booking && !$booking->is_paid) {
                    // Create payment failure record
                    \App\Models\PaymentFailure::create([
                        'booking_id' => $booking->id,
                        'stripe_payment_intent_id' => $paymentIntent['id'],
                        'stripe_session_id' => $booking->stripe_session_id,
                        'decline_code' => $declineCode,
                        'failure_reason' => $errorMessage,
                        'attempted_amount' => $paymentIntent['amount'] ?? $booking->total_price * 100,
                        'currency' => $paymentIntent['currency'] ?? 'gbp',
                        'payment_method' => 'stripe_checkout',
                        'stripe_error_data' => $lastPaymentError,
                        'failed_at' => now(),
                    ]);

                    // Update booking status and store payment intent ID
                    $booking->update([
                        'status' => 'payment_failed',
                        'stripe_payment_intent_id' => $paymentIntent['id'],
                    ]);

                    // Note: No email sent here - customer can retry within same Stripe session
                    // Email notifications handled by checkout.session.expired for final failures

                    Log::info('Payment failed for booking - status updated (no email sent)', [
                        'booking_id' => $booking->booking_id,
                        'previous_status' => $booking->getOriginal('status'),
                        'new_status' => 'payment_failed',
                        'stripe_payment_intent_id' => $paymentIntent['id'],
                        'decline_code' => $declineCode,
                        'reason' => 'Customer can retry within same session - no premature email needed'
                    ]);
                }
            }

            return ['status' => 'processed', 'message' => 'Payment failure processed and booking updated'];
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

            // Find the booking associated with this charge
            $booking = $this->findBookingByChargeId($dispute['charge']);

            if (!$booking) {
                Log::warning('No booking found for dispute charge', [
                    'dispute_id' => $dispute['id'],
                    'charge_id' => $dispute['charge']
                ]);
                return ['status' => 'ignored', 'message' => 'Booking not found for charge'];
            }

            // All bookings in our system are direct bookings since external bookings
            // (like Airbnb) wouldn't use our Stripe payment system
            // So we process all disputes for our bookings

            // Check if dispute already exists
            $existingDispute = BookingDispute::where('stripe_dispute_id', $dispute['id'])->first();
            if ($existingDispute) {
                Log::info('Dispute already exists', ['dispute_id' => $dispute['id']]);
                return ['status' => 'exists', 'message' => 'Dispute already recorded'];
            }

            // Create the dispute record
            $bookingDispute = BookingDispute::create([
                'booking_id' => $booking->id,
                'stripe_dispute_id' => $dispute['id'],
                'stripe_charge_id' => $dispute['charge'],
                'amount' => $dispute['amount'],
                'currency' => $dispute['currency'] ?? 'gbp',
                'reason' => $dispute['reason'],
                'status' => $dispute['status'],
                'evidence_details' => $dispute['evidence_details'] ?? null,
                'evidence_due_by' => isset($dispute['evidence_due_by']) ? \Carbon\Carbon::createFromTimestamp($dispute['evidence_due_by']) : null,
                'created_at_stripe' => \Carbon\Carbon::createFromTimestamp($dispute['created']),
                'admin_notified' => false,
            ]);

            // Send admin notification
            try {
                Mail::to(config('mail.owner_email', 'admin@example.com'))
                    ->send(new DisputeNotification($bookingDispute));

                $bookingDispute->update(['admin_notified' => true]);

                Log::info('Dispute notification sent successfully', [
                    'dispute_id' => $dispute['id'],
                    'booking_id' => $booking->booking_id
                ]);
            } catch (Exception $mailException) {
                Log::error('Failed to send dispute notification', [
                    'dispute_id' => $dispute['id'],
                    'error' => $mailException->getMessage()
                ]);
            }

            return ['status' => 'processed', 'message' => 'Dispute recorded and admin notified'];
        } catch (Exception $e) {
            Log::error('Exception in charge dispute created handler', [
                'error' => $e->getMessage(),
                'dispute_id' => $dispute['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handleChargeDisputeUpdated($dispute): array
    {
        try {
            Log::info('Charge dispute updated', [
                'dispute_id' => $dispute['id'],
                'status' => $dispute['status'],
                'evidence_due_by' => $dispute['evidence_due_by'] ?? null
            ]);

            // Find existing dispute record
            $bookingDispute = BookingDispute::where('stripe_dispute_id', $dispute['id'])->first();

            if (!$bookingDispute) {
                Log::warning('Dispute update received for unknown dispute', [
                    'dispute_id' => $dispute['id']
                ]);
                return ['status' => 'ignored', 'message' => 'Unknown dispute'];
            }

            // Update dispute record
            $bookingDispute->update([
                'status' => $dispute['status'],
                'evidence_details' => $dispute['evidence_details'] ?? $bookingDispute->evidence_details,
                'evidence_due_by' => isset($dispute['evidence_due_by']) ? \Carbon\Carbon::createFromTimestamp($dispute['evidence_due_by']) : $bookingDispute->evidence_due_by,
            ]);

            Log::info('Dispute updated successfully', [
                'dispute_id' => $dispute['id'],
                'new_status' => $dispute['status']
            ]);

            return ['status' => 'updated', 'message' => 'Dispute record updated'];
        } catch (Exception $e) {
            Log::error('Exception in charge dispute updated handler', [
                'error' => $e->getMessage(),
                'dispute_id' => $dispute['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    private function handleChargeDisputeClosed($dispute): array
    {
        try {
            Log::info('Charge dispute closed', [
                'dispute_id' => $dispute['id'],
                'status' => $dispute['status']
            ]);

            // Find existing dispute record
            $bookingDispute = BookingDispute::where('stripe_dispute_id', $dispute['id'])->first();

            if (!$bookingDispute) {
                Log::warning('Dispute closed event received for unknown dispute', [
                    'dispute_id' => $dispute['id']
                ]);
                return ['status' => 'ignored', 'message' => 'Unknown dispute'];
            }

            // Update dispute with final status
            $bookingDispute->update([
                'status' => $dispute['status'],
                'evidence_details' => $dispute['evidence_details'] ?? $bookingDispute->evidence_details,
            ]);

            Log::info('Dispute closed and record updated', [
                'dispute_id' => $dispute['id'],
                'final_status' => $dispute['status'],
                'booking_id' => $bookingDispute->booking->booking_id
            ]);

            return ['status' => 'closed', 'message' => 'Dispute closed and recorded'];
        } catch (Exception $e) {
            Log::error('Exception in charge dispute closed handler', [
                'error' => $e->getMessage(),
                'dispute_id' => $dispute['id'] ?? 'unknown'
            ]);
            return ['status' => 'error', 'message' => 'Internal error'];
        }
    }

    /**
     * Find booking by Stripe charge ID
     */
    private function findBookingByChargeId(string $chargeId): ?Booking
    {
        try {
            // Method 1: Use Stripe API to get payment intent from charge
            Log::info('Attempting to find booking by charge ID', ['charge_id' => $chargeId]);

            $charge = \Stripe\Charge::retrieve($chargeId);

            if ($charge->payment_intent) {
                Log::info('Found payment intent from charge', [
                    'charge_id' => $chargeId,
                    'payment_intent' => $charge->payment_intent
                ]);

                $booking = Booking::where('stripe_payment_intent_id', $charge->payment_intent)->first();
                if ($booking) {
                    Log::info('Found booking via payment intent', [
                        'booking_id' => $booking->booking_id,
                        'payment_intent' => $charge->payment_intent
                    ]);
                    return $booking;
                }
            }

            // Method 2: Skip direct charge ID check since column doesn't exist
            // TODO: Add stripe_charge_id column to bookings table if needed

            // Method 3: Try to find through ARN records
            $arn = Arn::whereHas('booking', function($query) use ($chargeId) {
                $query->where('stripe_charge_id', $chargeId);
            })->first();

            if ($arn) {
                Log::info('Found booking via ARN record', [
                    'booking_id' => $arn->booking->booking_id,
                    'charge_id' => $chargeId
                ]);
                return $arn->booking;
            }

            // Method 4: Find recent booking with similar payment intent pattern
            // This is a fallback for when charge/payment intent linking fails
            $recentBooking = Booking::where('is_paid', true)
                ->where('created_at', '>=', now()->subHours(24)) // Only recent bookings
                ->whereNotNull('stripe_payment_intent_id')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($recentBooking) {
                Log::warning('Using fallback: most recent paid booking for dispute', [
                    'charge_id' => $chargeId,
                    'fallback_booking_id' => $recentBooking->booking_id,
                    'reason' => 'Could not link charge to specific booking'
                ]);
                return $recentBooking;
            }

            Log::warning('No booking found for charge ID after all methods', [
                'charge_id' => $chargeId
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Error finding booking by charge ID', [
                'charge_id' => $chargeId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Emergency fallback: try to find any recent paid booking
            try {
                $fallbackBooking = Booking::where('is_paid', true)
                    ->where('created_at', '>=', now()->subHours(1))
                    ->latest()
                    ->first();

                if ($fallbackBooking) {
                    Log::warning('Using emergency fallback booking for dispute', [
                        'charge_id' => $chargeId,
                        'fallback_booking_id' => $fallbackBooking->booking_id,
                        'reason' => 'API error, using most recent booking'
                    ]);
                    return $fallbackBooking;
                }
            } catch (Exception $fallbackException) {
                Log::error('Even fallback booking lookup failed', [
                    'error' => $fallbackException->getMessage()
                ]);
            }

            return null;
        }
    }    private function handleCheckoutSessionExpired($session): array
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
                if ($booking && !$booking->is_paid && $booking->status !== 'abandoned') {
                    // Update booking status directly to abandoned (no emails, no grace period)
                    $booking->update([
                        'status' => 'abandoned',
                        'notes' => ($booking->notes ? $booking->notes . "\n" : '') .
                                  'Payment session expired at ' . now()->format('Y-m-d H:i:s') . ' - automatically marked as abandoned'
                    ]);

                    Log::info('Booking marked as abandoned due to session expiry', [
                        'booking_id' => $booking->getBookingReference(),
                        'booking_display_id' => $booking->getDisplayBookingId(),
                        'previous_status' => $booking->getOriginal('status'),
                        'session_id' => $session['id']
                    ]);
                } else if ($booking && $booking->status === 'abandoned') {
                    Log::info('Session expired webhook received for already abandoned booking - skipping duplicate processing', [
                        'booking_id' => $booking->getBookingReference(),
                        'booking_display_id' => $booking->getDisplayBookingId(),
                        'current_status' => $booking->status,
                        'session_id' => $session['id'],
                        'reason' => 'Duplicate webhook or retry - already abandoned'
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

    private function handleChargeRefunded($charge): array
    {
        try {
            // Validate charge structure
            if (!isset($charge['id'], $charge['payment_intent'], $charge['amount_refunded'])) {
                Log::error('Invalid charge structure in refund webhook', [
                    'charge_keys' => array_keys($charge),
                    'charge_id' => $charge['id'] ?? 'missing'
                ]);
                return ['status' => 'error', 'message' => 'Invalid charge structure'];
            }

            Log::info('Processing charge.refunded webhook', [
                'charge_id' => $charge['id'],
                'payment_intent_id' => $charge['payment_intent'],
                'amount_refunded' => $charge['amount_refunded']
            ]);

            // Find booking by payment_intent_id
            $booking = Booking::where('stripe_payment_intent_id', $charge['payment_intent'])->first();

            if (!$booking) {
                Log::warning('Booking not found for refunded charge', [
                    'payment_intent_id' => $charge['payment_intent'],
                    'charge_id' => $charge['id']
                ]);
                return ['status' => 'error', 'message' => 'Booking not found'];
            }

            // Convert from cents to pounds
            $refundAmount = $charge['amount_refunded'] / 100;
            $isFullRefund = $charge['amount_refunded'] === $charge['amount'];

            Log::info('Found booking for refund', [
                'booking_id' => $booking->booking_id,
                'booking_display_id' => $booking->getDisplayBookingId(),
                'refund_amount' => $refundAmount,
                'is_full_refund' => $isFullRefund
            ]);

            // Get refund details from the charge with proper null checking
            $refunds = $charge['refunds']['data'] ?? [];
            $refundData = isset($refunds[0]) ? $refunds[0] : null;
            $stripeRefundReason = $refundData['reason'] ?? 'requested_by_customer';
            $refundId = $refundData['id'] ?? null;

            // Enhanced logging to debug refunds structure
            Log::info('Charge refunds structure debug', [
                'booking_id' => $booking->booking_id,
                'charge_id' => $charge['id'],
                'has_refunds_key' => isset($charge['refunds']),
                'has_refunds_data_key' => isset($charge['refunds']['data']),
                'refunds_count' => count($refunds),
                'refunds_raw' => $refunds,
                'amount_refunded' => $charge['amount_refunded'],
                'charge_amount' => $charge['amount']
            ]);

            // Process each refund and capture ARN only if refunds exist
            if (!empty($refunds)) {
                foreach ($refunds as $refund) {
                    // Check if we already have this refund recorded
                    $existingArn = Arn::where('refund_id', $refund['id'])->first();

                if (!$existingArn) {
                    // Extract ARN from the refund object
                    $arnNumber = $refund['acquirer_reference_number'] ?? null;

                    Log::info('Capturing ARN from refund webhook', [
                        'booking_id' => $booking->booking_id,
                        'refund_id' => $refund['id'],
                        'arn_number' => $arnNumber,
                        'refund_amount' => $refund['amount'] / 100, // Convert cents to pounds
                        'refund_status' => $refund['status']
                    ]);

                    // Create ARN record
                    Arn::create([
                        'booking_id' => $booking->id,
                        'refund_id' => $refund['id'],
                        'arn_number' => $arnNumber,
                        'refund_amount' => $refund['amount'] / 100, // Convert cents to pounds
                        'currency' => $refund['currency'],
                        'status' => $refund['status'],
                        'refund_processed_at' => $refund['status'] === 'succeeded' ? now() : null,
                    ]);

                    Log::info('ARN record created successfully', [
                        'booking_id' => $booking->booking_id,
                        'refund_id' => $refund['id'],
                        'arn_number' => $arnNumber
                    ]);
                } else {
                    // Update existing ARN record with latest info
                    $arnNumber = $refund['acquirer_reference_number'] ?? $existingArn->arn_number;

                    $existingArn->update([
                        'arn_number' => $arnNumber,
                        'status' => $refund['status'],
                        'refund_processed_at' => $refund['status'] === 'succeeded' ? now() : $existingArn->refund_processed_at,
                    ]);

                    Log::info('ARN record updated', [
                        'booking_id' => $booking->booking_id,
                        'refund_id' => $refund['id'],
                        'arn_number' => $arnNumber,
                        'status' => $refund['status']
                    ]);
                }
            }
            } else {
                Log::warning('No refunds found in charge webhook', [
                    'booking_id' => $booking->booking_id,
                    'charge_id' => $charge['id'] ?? 'unknown'
                ]);
            }

            // Create a meaningful reason combining stripe reason and refund type
            $refundType = $isFullRefund ? 'Full refund' : 'Partial refund';
            $combinedReason = $refundType . ' processed via Stripe (Reason: ' . $stripeRefundReason . ')';

            Log::info('Refund details extracted', [
                'stripe_reason' => $stripeRefundReason,
                'refund_type' => $refundType,
                'combined_reason' => $combinedReason,
                'refund_id' => $refundId
            ]);

            // Call your existing processRefund method
            Log::info('About to call PaymentSuccessService->processRefund', [
                'booking_id' => $booking->booking_id,
                'refund_amount' => $refundAmount,
                'combined_reason' => $combinedReason
            ]);

            $result = $this->paymentSuccessService->processRefund(
                $booking,
                $refundAmount,
                $combinedReason
            );

            Log::info('PaymentSuccessService->processRefund result', [
                'booking_id' => $booking->booking_id,
                'result_success' => $result['success'] ?? 'unknown',
                'result_message' => $result['message'] ?? 'no message'
            ]);

            if ($result['success']) {
                Log::info('Refund webhook processed successfully', [
                    'booking_id' => $booking->booking_id,
                    'booking_display_id' => $booking->getDisplayBookingId(),
                    'refund_amount' => $refundAmount,
                    'is_full_refund' => $isFullRefund,
                    'refund_id' => $refundId
                ]);

                return ['status' => 'success', 'message' => 'Refund processed successfully'];
            } else {
                Log::error('Failed to process refund in database', [
                    'booking_id' => $booking->booking_id,
                    'refund_amount' => $refundAmount,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);

                return ['status' => 'error', 'message' => 'Failed to update database: ' . ($result['error'] ?? 'Unknown error')];
            }

        } catch (Exception $e) {
            Log::error('Exception in charge refunded handler', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $charge['payment_intent'] ?? 'unknown',
                'charge_id' => $charge['id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return ['status' => 'error', 'message' => 'Internal error: ' . $e->getMessage()];
        }
    }

    private function handleRefundCreated($refund): array
    {
        try {
            Log::info('Processing refund.created webhook', [
                'refund_id' => $refund->id,
                'charge_id' => $refund->charge,
                'amount' => $refund->amount / 100,
                'status' => $refund->status,
                'reason' => $refund->reason ?? 'unknown'
            ]);

            // We need to get the payment intent from the charge
            // For now, let's try to find booking by charge ID in existing refund webhooks
            // or find by payment_intent if it's provided in the refund object
            $booking = null;

            // First try to find by payment_intent if available in refund
            if (isset($refund->payment_intent)) {
                $booking = Booking::where('stripe_payment_intent_id', $refund->payment_intent)->first();
                Log::info('Trying to find booking by payment_intent from refund', [
                    'payment_intent' => $refund->payment_intent,
                    'found' => $booking ? 'yes' : 'no'
                ]);
            }

            // If not found and we have charge ID, we could make a Stripe API call to get the charge
            // but for now let's log what we have and see if payment_intent is in the refund object
            if (!$booking) {
                // Convert Stripe object to array for debugging
                $refundArray = $refund->toArray();
                Log::warning('No booking found for refund.created webhook - debugging refund structure', [
                    'refund_id' => $refund->id,
                    'charge_id' => $refund->charge,
                    'refund_keys' => array_keys($refundArray),
                    'has_payment_intent' => isset($refund->payment_intent),
                    'payment_intent' => $refund->payment_intent ?? 'not_present'
                ]);
                return ['status' => 'ignored', 'message' => 'No booking found for this refund'];
            }

            // Check if we already have this refund recorded
            $existingArn = Arn::where('refund_id', $refund->id)->first();

            if ($existingArn) {
                Log::info('ARN already exists for refund', [
                    'booking_id' => $booking->booking_id,
                    'refund_id' => $refund->id,
                    'existing_arn' => $existingArn->arn_number
                ]);
                return ['status' => 'ignored', 'message' => 'ARN already recorded'];
            }

            // Extract ARN from the refund object - check multiple possible locations
            $arnNumber = null;

            // Method 1: Direct field (older Stripe API versions)
            if (isset($refund->acquirer_reference_number)) {
                $arnNumber = $refund->acquirer_reference_number;
            }

            // Method 2: Check destination_details.card.reference (newer structure)
            if (!$arnNumber && isset($refund->destination_details['card']['reference'])) {
                $arnNumber = $refund->destination_details['card']['reference'];
            }

            // Method 3: Check if ARN is in metadata or other nested fields
            if (!$arnNumber && isset($refund->destination_details['card']['acquirer_reference_number'])) {
                $arnNumber = $refund->destination_details['card']['acquirer_reference_number'];
            }

            // Enhanced logging to debug ARN fields
            $refundArray = $refund->toArray();
            Log::info('Capturing ARN from refund.created webhook - full debug', [
                'booking_id' => $booking->booking_id,
                'refund_id' => $refund->id,
                'arn_number' => $arnNumber,
                'refund_amount' => $refund->amount / 100,
                'refund_status' => $refund->status,
                'refund_reason' => $refund->reason ?? 'unknown',
                'all_refund_keys' => array_keys($refundArray),
                'complete_refund_object' => $refundArray, // Log the entire object to see all fields
                'has_acquirer_reference_number' => isset($refund->acquirer_reference_number),
                'acquirer_reference_number_value' => $refund->acquirer_reference_number ?? 'not_present'
            ]);

            // Create ARN record
            $arn = Arn::create([
                'booking_id' => $booking->id,
                'refund_id' => $refund->id,
                'arn_number' => $arnNumber,
                'refund_amount' => $refund->amount / 100,
                'currency' => $refund->currency,
                'status' => $refund->status,
                'refund_processed_at' => $refund->status === 'succeeded' ? now() : null,
            ]);

            Log::info('ARN record created successfully from refund.created webhook', [
                'booking_id' => $booking->booking_id,
                'refund_id' => $refund->id,
                'arn_number' => $arnNumber,
                'arn_id' => $arn->id
            ]);

            return ['status' => 'success', 'message' => 'ARN captured from refund.created webhook'];

        } catch (Exception $e) {
            Log::error('Exception in refund.created handler', [
                'error' => $e->getMessage(),
                'refund_id' => $refund->id ?? 'unknown',
                'charge_id' => $refund->charge ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return ['status' => 'error', 'message' => 'Internal error: ' . $e->getMessage()];
        }
    }

    private function handleRefundUpdated($refund): array
    {
        try {
            Log::info('Processing refund.updated webhook', [
                'refund_id' => $refund->id,
                'charge_id' => $refund->charge,
                'status' => $refund->status
            ]);

            // Find existing ARN record
            $arn = Arn::where('refund_id', $refund->id)->first();

            if (!$arn) {
                Log::info('No existing ARN record found for refund update', [
                    'refund_id' => $refund->id
                ]);
                return ['status' => 'ignored', 'message' => 'No existing ARN record'];
            }

            // Extract ARN from the updated refund object
            $arnNumber = null;

            // Method 1: Direct field
            if (isset($refund->acquirer_reference_number)) {
                $arnNumber = $refund->acquirer_reference_number;
            }

            // Method 2: Check destination_details.card.reference
            if (!$arnNumber && isset($refund->destination_details['card']['reference'])) {
                $arnNumber = $refund->destination_details['card']['reference'];
            }

            // Method 3: Check other possible ARN locations
            if (!$arnNumber && isset($refund->destination_details['card']['acquirer_reference_number'])) {
                $arnNumber = $refund->destination_details['card']['acquirer_reference_number'];
            }

            // Update ARN if we found one
            if ($arnNumber && $arnNumber !== $arn->arn_number) {
                $arn->update([
                    'arn_number' => $arnNumber,
                    'status' => $refund->status,
                    'refund_processed_at' => $refund->status === 'succeeded' ? now() : null,
                ]);

                Log::info('ARN updated from refund.updated webhook', [
                    'refund_id' => $refund->id,
                    'old_arn' => $arn->arn_number,
                    'new_arn' => $arnNumber,
                    'arn_id' => $arn->id
                ]);

                return ['status' => 'success', 'message' => 'ARN updated'];
            }

            Log::info('No ARN update needed', [
                'refund_id' => $refund->id,
                'current_arn' => $arn->arn_number,
                'found_arn' => $arnNumber
            ]);

            return ['status' => 'ignored', 'message' => 'No ARN changes'];

        } catch (Exception $e) {
            Log::error('Exception in refund.updated handler', [
                'error' => $e->getMessage(),
                'refund_id' => $refund->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return ['status' => 'error', 'message' => 'Internal error: ' . $e->getMessage()];
        }
    }
}
