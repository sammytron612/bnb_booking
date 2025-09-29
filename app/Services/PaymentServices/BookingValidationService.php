<?php

namespace App\Services\PaymentServices;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingValidationService
{
    /**
     * Validate booking access with enhanced security checks
     *
     * @param Request $request
     * @param Booking $booking
     * @param bool $requireSignature Whether to require signed URL validation
     * @return bool
     */
    public function validateBookingAccess(Request $request, Booking $booking, bool $requireSignature = true): bool
    {
        // Check signed URL signature only if required (checkout needs it, success/cancel don't)
        if ($requireSignature && !$request->hasValidSignature()) {
            $this->logSecurityWarning('Invalid signature for booking access', $request, $booking);
            return false;
        }

        // Additional basic validation - booking must exist and be recent
        if (!$booking || $booking->created_at < now()->subHours(48)) {
            $this->logSecurityWarning('Booking access validation failed - booking too old or missing', $request, $booking);
            return false;
        }

        // For success/cancel routes without signatures, require valid Stripe session ID
        if (!$requireSignature) {
            return $this->validateSessionAccess($request, $booking);
        }

        return true;
    }

    /**
     * Validate session access for success/cancel pages
     *
     * @param Request $request
     * @param Booking $booking
     * @return bool
     */
    private function validateSessionAccess(Request $request, Booking $booking): bool
    {
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

        return true;
    }

    /**
     * Log security warnings with comprehensive context
     *
     * @param string $message
     * @param Request $request
     * @param Booking|null $booking
     * @return void
     */
    private function logSecurityWarning(string $message, Request $request, ?Booking $booking): void
    {
        Log::warning($message, [
            'booking_id' => $booking?->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Validate that a booking is in a valid state for payment processing
     *
     * @param Booking $booking
     * @return bool
     */
    public function validateBookingForPayment(Booking $booking): bool
    {
        // Booking must exist
        if (!$booking) {
            return false;
        }

        // Booking must not be already paid
        if ($booking->is_paid) {
            return false;
        }

        // Booking must not be cancelled
        if ($booking->status === 'cancelled') {
            return false;
        }

        // Booking must be recent (within 48 hours)
        if ($booking->created_at < now()->subHours(48)) {
            return false;
        }

        return true;
    }
}
