<?php
// app/Mail/BookingConfirmation.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class NewBooking extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        // Ensure venue relationship is loaded
        $this->booking->load('venue');
    }

    public function build()
    {
        return $this->subject('New Booking - ' . $this->booking->getDisplayBookingId() . ' - ' . $this->booking->venue->venue_name)
                    ->view('emails.new-booking');
    }
}

