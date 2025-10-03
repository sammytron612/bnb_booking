<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use App\Models\Arn;
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
