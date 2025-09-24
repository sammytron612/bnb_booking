<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class CheckinMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('venue');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Check-in Reminder - ' . $this->booking->getDisplayBookingId() . ' - ' . $this->booking->venue->venue_name)
                    ->view('emails.checkin');
    }
}
