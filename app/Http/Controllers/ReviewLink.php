<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\Booking;
use App\Mail\ReviewLink as ReviewLinkMail;
use Illuminate\Support\Facades\Mail;

class ReviewLink extends Controller
{
    public function create()
    {
        $bookings = Booking::where('check_out', '>', now()->addHours(24))->get();

        // Check if booking exists
        if (!$bookings) {
            return redirect()->route('home')->with('error', 'Booking not found!');
        }

        foreach($bookings as $booking);
        {
            // Generate a signed review link that expires in 48 hours
            $reviewLink = URL::temporarySignedRoute(
                'reviews.create',
                now()->addHours(48),
                ['booking' => $booking->id]
            );

            // create the data array
            $data = [
                'reviewLink' => $reviewLink,
                'booking_id' => $booking->id,
                'name' => $booking->name,
                'venue' => $booking->venue
            ];

            // Send the review link via email
            Mail::to($booking->email)->send(new ReviewLinkMail($data));

        }


        die();

    }
}
