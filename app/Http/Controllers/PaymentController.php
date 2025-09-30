<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Services\PaymentServices\PaymentService;
use App\Services\PaymentServices\WebhookService;
use App\Services\PaymentServices\PaymentSuccessService;
use App\Services\PaymentServices\BookingValidationService;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;
use Exception;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $webhookService;
    protected $paymentSuccessService;
    protected $bookingValidationService;

    public function __construct(
        PaymentService $paymentService,
        WebhookService $webhookService,
        PaymentSuccessService $paymentSuccessService,
        BookingValidationService $bookingValidationService
    ) {
        $this->paymentService = $paymentService;
        $this->webhookService = $webhookService;
        $this->paymentSuccessService = $paymentSuccessService;
        $this->bookingValidationService = $bookingValidationService;
    }

    /**
     * Validate that the request has proper access to the booking
     */
    private function validateBookingAccess(Request $request, Booking $booking, bool $requireSignature = true): bool
    {
        return $this->bookingValidationService->validateBookingAccess($request, $booking, $requireSignature);
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
        if (!isset($session->metadata['booking_id']) || $session->metadata['booking_id'] != $booking->getBookingReference()) {
            Log::warning('Booking ID mismatch in verification', [
                'session_booking_id' => $session->metadata['booking_id'] ?? 'not_set',
                'booking_reference' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'booking_db_id' => $booking->id
            ]);
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

        // Validate booking for payment
        $validationErrors = $this->bookingValidationService->validateBookingForPayment($booking);
        if (!empty($validationErrors)) {
            return redirect()->route('home')->with('error', implode(' ', $validationErrors));
        }

        try {
            // Set Stripe API key before creating session
            $this->setStripeKey();

            $session = $this->paymentService->createCheckoutSession($booking);

            if (!$session) {
                throw new Exception('Failed to create checkout session');
            }

            // Store session ID in booking
            Log::info('Updating booking with Stripe session data', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'session_id' => $session->id,
                'amount' => $booking->total_price
            ]);

            // Set Stripe amount using total_price converted to cents, then back to decimal
            $booking->setStripeAmountFromCents((int)($booking->total_price * 100));
            $booking->stripe_session_id = $session->id;
            $booking->stripe_currency = 'gbp';

            $updateResult = $booking->save();

            Log::info('Booking update result', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'update_successful' => $updateResult,
                'fresh_stripe_session_id' => $booking->fresh()->stripe_session_id
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
            Log::error('Failed to create checkout session', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'error' => $e->getMessage()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unable to create payment session. Please try again.',
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
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'provided_session_id' => $sessionId,
                'stored_session_id' => $booking->stripe_session_id,
                'ip' => $request->ip()
            ]);
            return redirect()->route('home')->with('error', 'Invalid payment session.');
        }

        try {
            $session = StripeSession::retrieve($sessionId);

            // Enhanced payment verification
            if (!$this->verifyStripePayment($session, $booking)) {
                Log::warning('Payment verification failed', [
                    'booking_id' => $booking->getBookingReference(),
                    'booking_display_id' => $booking->getDisplayBookingId(),
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
                    'stripe_amount' => $session->amount_total, // Amount in cents
                    'stripe_currency' => $session->currency, // Should be 'gbp'
                ];

                // Update email if customer provided one in Stripe checkout - with validation
                if (!empty($session->customer_email)) {
                    $newEmail = filter_var($session->customer_email, FILTER_VALIDATE_EMAIL);
                    if ($newEmail && $this->validateEmailChange($booking->email, $newEmail)) {
                        $updateData['email'] = $newEmail;
                        Log::info('Email updated for booking via success page', [
                            'booking_id' => $booking->getBookingReference(),
                            'booking_display_id' => $booking->getDisplayBookingId(),
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
        $result = $this->webhookService->handleWebhook($request);

        if ($result['status'] === 'error') {
            return response($result['message'], 400);
        }

        return response('', 200);
    }

    /**
     * Resume payment for expired booking session
     */
    public function resumePayment(Request $request, Booking $booking)
    {
        // Validate the signed URL
        if (!$request->hasValidSignature()) {
            return redirect()->route('home')->with('error', 'Invalid or expired payment link.');
        }

        // Check if booking is eligible for payment resumption
        if ($booking->is_paid) {
            return redirect()->route('payment.success', ['booking' => $booking->id])
                ->with('info', 'This booking has already been paid.');
        }

        if ($booking->status === 'cancelled') {
            return redirect()->route('home')
                ->with('error', 'This booking has been cancelled and cannot be paid.');
        }

        // Check if booking has expired (older than 24 hours from creation)
        if ($booking->created_at->addHours(24)->isPast()) {
            return redirect()->route('home')
                ->with('error', 'This booking has expired. Please make a new reservation.');
        }

        try {
            $this->setStripeKey();

            // Create new checkout session for the same booking
            $session = $this->paymentService->createCheckoutSession($booking);

            if (!$session) {
                throw new Exception('Failed to create new payment session');
            }

            // Update booking with new session ID
            $booking->update([
                'stripe_session_id' => $session->id,
                'status' => 'pending', // Reset from payment_expired to pending
            ]);

            Log::info('Payment session resumed', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'new_session_id' => $session->id,
                'previous_status' => $booking->getOriginal('status')
            ]);

            // Redirect to Stripe checkout
            return redirect($session->url);

        } catch (Exception $e) {
            Log::error('Failed to resume payment session', [
                'booking_id' => $booking->getBookingReference(),
                'booking_display_id' => $booking->getDisplayBookingId(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('home')->with('error', 'Unable to resume payment. Please try making a new booking.');
        }
    }
}
