<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PaymentExpired extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Complete Your Booking Payment - ' . $this->booking->getDisplayBookingId(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-expired',
            with: [
                'booking' => $this->booking,
                'resumePaymentUrl' => $this->generateResumePaymentUrl(),
                'expiryDate' => now()->addHours(24)->format('M j, Y \a\t g:i A'),
            ],
        );
    }

    private function generateResumePaymentUrl(): string
    {
        return URL::temporarySignedRoute(
            'payment.resume',
            now()->addHours(24),
            ['booking' => $this->booking->id]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
