<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Exception;

class PaymentSuccessService
{
    public function processSuccessfulPayment(
        Booking $booking,
        string $paymentIntentId,
        string $paymentMethod = 'stripe',
        ?array $additionalData = null
    ): array {
        try {
            DB::beginTransaction();

            // Update booking payment status
            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'payment_intent_id' => $paymentIntentId,
                'pay_method' => $paymentMethod,
                'paid_at' => now(),
                'stripe_amount' => $booking->getStripeAmountInCents(),
                'stripe_currency' => 'gbp',
            ]);

            // Log the successful payment
            Log::info('Payment processed successfully', [
                'booking_id' => $booking->booking_id,
                'payment_intent_id' => $paymentIntentId,
                'amount' => $booking->total_price,
                'payment_method' => $paymentMethod,
                'customer_email' => $booking->email
            ]);

            // Send confirmation emails
            $this->sendConfirmationEmails($booking);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'booking' => $booking
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to process successful payment', [
                'booking_id' => $booking->booking_id,
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ];
        }
    }

    private function sendConfirmationEmails(Booking $booking): void
    {
        // Check if confirmation email was already sent (prevents duplicates)
        if ($booking->confirmation_email_sent) {
            Log::info('Confirmation email already sent, skipping', [
                'booking_id' => $booking->booking_id,
                'confirmation_email_sent' => $booking->confirmation_email_sent->format('Y-m-d H:i:s')
            ]);
            return;
        }

        try {
            // Send confirmation email to customer
            Mail::to($booking->email)->send(new BookingConfirmation($booking));

            // Mark confirmation email as sent
            $booking->update(['confirmation_email_sent' => now()]);

            Log::info('Booking confirmation email sent to customer', [
                'booking_id' => $booking->booking_id,
                'customer_email' => $booking->email,
                'confirmation_email_sent' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send booking confirmation email to customer', [
                'booking_id' => $booking->booking_id,
                'customer_email' => $booking->email,
                'error' => $e->getMessage()
            ]);
        }

        try {
            // Send notification email to admin
            $adminEmail = config('mail.owner_email', 'admin@eileenbnb.com');
            Mail::to($adminEmail)->send(new NewBooking($booking));

            Log::info('New booking notification email sent to admin', [
                'booking_id' => $booking->booking_id,
                'admin_email' => $adminEmail
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send new booking notification email to admin', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handlePaymentFailure(
        Booking $booking,
        string $reason = 'Payment failed',
        ?array $additionalData = null
    ): array {
        try {
            // Update booking status but don't delete it
            $booking->update([
                'status' => 'payment_failed',
                'notes' => $booking->notes ? $booking->notes . "\n" . $reason : $reason
            ]);

            Log::warning('Payment failed for booking', [
                'booking_id' => $booking->booking_id,
                'reason' => $reason,
                'customer_email' => $booking->email,
                'additional_data' => $additionalData
            ]);

            return [
                'success' => true,
                'message' => 'Payment failure recorded',
                'booking' => $booking
            ];
        } catch (Exception $e) {
            Log::error('Failed to handle payment failure', [
                'booking_id' => $booking->booking_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to handle payment failure: ' . $e->getMessage()
            ];
        }
    }

    public function processRefund(
        Booking $booking,
        float $refundAmount,
        string $reason = 'Customer requested refund'
    ): array {
        try {
            DB::beginTransaction();

            // Determine if it's a full or partial refund
            $isPartialRefund = $refundAmount < (float) $booking->total_price;
            $refundStatus = $isPartialRefund ? 'partial_refund' : 'refunded';

            // Update booking with refund information
            $booking->update([
                'status' => $refundStatus,
                'refund_amount' => $refundAmount,
                'refund_reason' => $reason,
                'refunded_at' => now(),
                'notes' => $booking->notes ? $booking->notes . "\n" . "Refunded: £{$refundAmount} - {$reason}" : "Refunded: £{$refundAmount} - {$reason}"
            ]);

            Log::info('Refund processed for booking', [
                'booking_id' => $booking->booking_id,
                'refund_amount' => $refundAmount,
                'total_amount' => $booking->total_price,
                'is_partial_refund' => $isPartialRefund,
                'status' => $refundStatus,
                'reason' => $reason,
                'customer_email' => $booking->email
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Refund processed successfully',
                'booking' => $booking
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to process refund', [
                'booking_id' => $booking->booking_id,
                'refund_amount' => $refundAmount,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage()
            ];
        }
    }
}
