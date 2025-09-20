<?php
// app/Mail/BookingConfirmation.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('venue');
    }

    public function build()
    {
        return $this->subject('Booking Confirmation - ' . $this->booking->getDisplayBookingId() . ' - ' . $this->booking->venue->venue_name)
                    ->view('emails.booking-confirmation');
    }
}
