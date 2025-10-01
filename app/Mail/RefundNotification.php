<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $refundAmount;
    public $refundReason;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $refundAmount, $refundReason)
    {
        $this->booking = $booking;
        $this->refundAmount = $refundAmount;
        $this->refundReason = $refundReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Refund Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.refund-notification',
            with: [
                'booking' => $this->booking,
                'refundAmount' => $this->refundAmount,
                'refundReason' => $this->refundReason,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
