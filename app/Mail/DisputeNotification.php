<?php

namespace App\Mail;

use App\Models\BookingDispute;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DisputeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookingDispute $dispute
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->dispute->friendly_status;
        $amount = $this->dispute->amount_in_pounds;

        return new Envelope(
            subject: "🚨 Payment Dispute Alert - £{$amount} - {$status}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dispute-notification',
            with: [
                'dispute' => $this->dispute,
                'booking' => $this->dispute->booking,
                'guest' => $this->dispute->booking->name ?? 'Unknown Guest',
                'stripeDisputeUrl' => "https://dashboard.stripe.com/payments/{$this->dispute->stripe_charge_id}"
            ]
        );
    }
}
