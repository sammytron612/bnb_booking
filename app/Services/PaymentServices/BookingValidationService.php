<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingValidationService
{
    /**
     * Validate that the request has proper access to the booking
     */
    public function validateBookingAccess(Request $request, Booking $booking, bool $requireSignature = true): bool
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

        return true;
    }

    /**
     * Validate booking data for payment processing
     */
    public function validateBookingForPayment(Booking $booking): array
    {
        $errors = [];

        // Check if booking is already paid
        if ($booking->is_paid) {
            $errors[] = 'This booking has already been paid for.';
        }

        // Check if booking is cancelled
        if ($booking->status === 'cancelled') {
            $errors[] = 'This booking has been cancelled and cannot be paid for.';
        }

        // Check if booking is too old (more than 48 hours)
        if ($booking->created_at < now()->subHours(48)) {
            $errors[] = 'This booking is too old to process payment. Please contact us directly.';
        }

        // Check if check-in date is in the past
        if (Carbon::parse($booking->check_in)->isPast()) {
            $errors[] = 'The check-in date for this booking has already passed.';
        }

        // Validate booking amount
        if ($booking->total_price <= 0) {
            $errors[] = 'Invalid booking amount.';
        }

        // Check if venue still exists and is active
        if (!$booking->venue) {
            $errors[] = 'The venue for this booking is no longer available.';
        }

        return $errors;
    }

    /**
     * Check if booking can be cancelled
     */
    public function canCancelBooking(Booking $booking): array
    {
        $errors = [];
        $checkInDate = Carbon::parse($booking->check_in);
        $now = now();

        // Check if booking is already cancelled
        if ($booking->status === 'cancelled') {
            $errors[] = 'This booking is already cancelled.';
        }

        // Check if check-in has already passed
        if ($checkInDate->isPast()) {
            $errors[] = 'Cannot cancel a booking after the check-in date has passed.';
        }

        // Check cancellation policy (24 hours before check-in)
        if ($checkInDate->diffInHours($now) < 24) {
            $errors[] = 'Bookings cannot be cancelled less than 24 hours before check-in.';
        }

        return $errors;
    }

    /**
     * Check if booking can be modified
     */
    public function canModifyBooking(Booking $booking): array
    {
        $errors = [];
        $checkInDate = Carbon::parse($booking->check_in);
        $now = now();

        // Check if booking is cancelled
        if ($booking->status === 'cancelled') {
            $errors[] = 'Cannot modify a cancelled booking.';
        }

        // Check if check-in has already passed
        if ($checkInDate->isPast()) {
            $errors[] = 'Cannot modify a booking after the check-in date has passed.';
        }

        // Check modification policy (48 hours before check-in)
        if ($checkInDate->diffInHours($now) < 48) {
            $errors[] = 'Bookings cannot be modified less than 48 hours before check-in.';
        }

        return $errors;
    }

    /**
     * Validate refund eligibility
     */
    public function validateRefundEligibility(Booking $booking): array
    {
        $errors = [];
        $checkInDate = Carbon::parse($booking->check_in);
        $now = now();

        // Check if booking is paid
        if (!$booking->is_paid) {
            $errors[] = 'Cannot refund an unpaid booking.';
        }

        // Check if already refunded
        if ($booking->status === 'refunded') {
            $errors[] = 'This booking has already been refunded.';
        }

        // Check if check-in has passed (no refund after check-in)
        if ($checkInDate->isPast()) {
            $errors[] = 'Cannot refund a booking after the check-in date has passed.';
        }

        return $errors;
    }

    /**
     * Calculate refund amount based on cancellation policy
     */
    public function calculateRefundAmount(Booking $booking): array
    {
        $checkInDate = Carbon::parse($booking->check_in);
        $now = now();
        $hoursUntilCheckIn = $checkInDate->diffInHours($now);
        $totalAmount = $booking->total_price;

        // Refund policy:
        // - More than 7 days: 100% refund
        // - 3-7 days: 50% refund
        // - 1-3 days: 25% refund
        // - Less than 24 hours: No refund

        if ($hoursUntilCheckIn >= (7 * 24)) {
            // More than 7 days
            $refundAmount = $totalAmount;
            $refundPercentage = 100;
        } elseif ($hoursUntilCheckIn >= (3 * 24)) {
            // 3-7 days
            $refundAmount = $totalAmount * 0.5;
            $refundPercentage = 50;
        } elseif ($hoursUntilCheckIn >= 24) {
            // 1-3 days
            $refundAmount = $totalAmount * 0.25;
            $refundPercentage = 25;
        } else {
            // Less than 24 hours
            $refundAmount = 0;
            $refundPercentage = 0;
        }

        return [
            'refund_amount' => round($refundAmount, 2),
            'refund_percentage' => $refundPercentage,
            'hours_until_checkin' => $hoursUntilCheckIn,
            'total_amount' => $totalAmount
        ];
    }
}
