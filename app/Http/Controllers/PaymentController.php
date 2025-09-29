<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Booking;
use App\Services\PaymentServices\PaymentService;
use App\Services\PaymentServices\WebhookService;
use App\Services\PaymentServices\PaymentSuccessService;
use App\Services\PaymentServices\BookingValidationService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Exception;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private WebhookService $webhookService,
        private PaymentSuccessService $paymentSuccessService,
        private BookingValidationService $bookingValidationService
    ) {
        // Stripe API key will be set per-method for better security
    }



    /**
     * Set Stripe API key securely for individual operations
     */
    private function setStripeKey(): void
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }



    /**
     * Create a Stripe checkout session for a booking
     */
    public function createCheckoutSession(Request $request, Booking $booking)
    {
        // Validate booking access (requires signature for checkout)
        if (!$this->bookingValidationService->validateBookingAccess($request, $booking, true)) {
            return redirect()->route('home')->with('error', 'Invalid or expired booking link.');
        }

        $this->setStripeKey();

        try {
            // Enhanced validation - ensure booking is in valid state
            if ($booking->is_paid) {
                return redirect()->route('home')->with('error', 'This booking has already been paid.');
            }

            // Use PaymentService to create checkout session
            $session = $this->paymentService->createCheckoutSession($booking);

            // Use PaymentService to update booking with session details
            $this->paymentService->updateBookingWithSession($booking, $session);

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
        if (!$this->bookingValidationService->validateBookingAccess($request, $booking, false)) {
            return redirect()->route('home')->with('error', 'Invalid or expired payment link.');
        }

        $this->setStripeKey();

        $sessionId = $request->get('session_id');

        // Validate session ID matches booking
        if (!$this->paymentSuccessService->validateSessionId($sessionId, $booking)) {
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

            // Process payment success using service
            if (!$this->paymentSuccessService->processPaymentSuccess($session, $booking)) {
                return redirect()->route('home')->with('error', 'Payment verification failed.');
            }

        } catch (Exception $e) {
            // Log error but still show success page
            Log::error('Failed to update booking after successful payment: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'session_id' => $sessionId
            ]);
        }

        return view('payment.success', compact('booking'));
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Request $request, Booking $booking)
    {
        // Validate booking access (no signature required for Stripe redirects)
        if (!$this->bookingValidationService->validateBookingAccess($request, $booking, false)) {
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

        // Handle the event using WebhookService
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->webhookService->handleCheckoutSessionCompleted($event['data']['object']);
                break;
            case 'payment_intent.created':
                $this->webhookService->handlePaymentIntentCreated($event['data']['object']);
                break;
            case 'payment_intent.succeeded':
                $this->webhookService->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->webhookService->handlePaymentIntentFailed($event['data']['object']);
                break;
            case 'charge.updated':
                $this->webhookService->handleChargeUpdated($event['data']['object']);
                break;
            case 'charge.succeeded':
                $this->webhookService->handleChargeSucceeded($event['data']['object']);
                break;
            default:
                Log::info('Unhandled webhook event type: ' . $event['type']);
        }

        return response('', 200);
    }


}
