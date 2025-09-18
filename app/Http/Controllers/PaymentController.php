<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
        Stripe::setApiKey(config('services.stripe.secret_key'));
    }

    /**
     * Create a Stripe checkout session for a booking
     */
    public function createCheckoutSession(Request $request, Booking $booking)
    {
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'gbp',
                        'product_data' => [
                            'name' => $booking->venue . ' Booking',
                            'description' => 'Booking from ' . Carbon::parse($booking->depart)->format('M j, Y') . ' to ' . Carbon::parse($booking->leave)->format('M j, Y'),
                        ],
                        'unit_amount' => (int) ($booking->total_price * 100), // Convert to pence
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $booking->email, // Pre-fill email from booking
                'success_url' => route('payment.success', ['booking' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['booking' => $booking->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                ],
            ]);

            // Store session ID in booking
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
        $sessionId = $request->get('session_id');

        if ($sessionId && $booking->stripe_session_id === $sessionId) {
            try {
                $session = Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    $updateData = [
                        'is_paid' => true,
                        'status' => 'confirmed',
                        'payment_completed_at' => now(),
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'pay_method' => 'stripe',
                    ];

                    // Update email if customer provided one in Stripe checkout
                    if (!empty($session->customer_email)) {
                        $updateData['email'] = $session->customer_email;
                    }

                    $booking->update($updateData);

                    // Send confirmation email
                    try {
                        //Mail::to($booking->email)->send(new BookingConfirmation($booking));
                        //Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                        Log::info('Booking confirmation email sent', ['booking_id' => $booking->id]);
                    } catch (Exception $e) {
                        Log::error('Failed to send confirmation email: ' . $e->getMessage());
                    }
                }
            } catch (Exception $e) {
                // Log error but still show success page
                Log::error('Failed to update booking after successful payment: ' . $e->getMessage());
            }
        }



        return view('payment.success', compact('booking'));
    }

    /**
     * Handle cancelled payment
     */
    public function paymentCancel(Booking $booking)
    {
        return view('payment.cancel', compact('booking'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response('', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event['data']['object']);
                break;
            default:
                Log::info('Received unknown event type: ' . $event['type']);
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
                $updateData = [
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                    'stripe_payment_intent_id' => $session['payment_intent'],
                    'stripe_metadata' => $session['metadata'],
                    'pay_method' => 'stripe',
                ];

                // Update email if customer provided one in Stripe checkout
                if (!empty($session['customer_email'])) {
                    $updateData['email'] = $session['customer_email'];
                }

                $booking->update($updateData);


                // Send emails
                Mail::to($booking->email)->send(new BookingConfirmation($booking));
                Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));

                Log::info('Booking payment confirmed via webhook', ['booking_id' => $bookingId]);
            }
        }
    }

    /**
     * Handle payment intent succeeded webhook
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Additional handling if needed
        Log::info('Payment intent succeeded', ['payment_intent_id' => $paymentIntent['id']]);
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
}
