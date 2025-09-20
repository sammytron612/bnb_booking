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
        //USE THIS TO GENERATE REVIEW LINKS AND EMAIL TO USERS AS A JOB
        $bookings = Booking::with('venue')
            ->where('check_out', '<', now()->addHours(24))
            ->whereDoesntHave('reviews')
            ->get();
dd($bookings);
       /* $bookings = Booking::with('venue')
        ->where('check_out', '>', now()->addHours(24))
        ->whereDoesntHave('reviews')
        ->find(1);*/

        foreach($bookings as $booking)
        {
            // Generate a signed review link that expires in 72 hours
            $reviewLink = URL::temporarySignedRoute(
                'reviews.create',
                now()->addHours(96),
                ['booking' => $booking->id]
            );

            // create the data array
            $data = [
                'reviewLink' => $reviewLink,
                'booking_id' => $booking->id,
                'name' => $booking->name,
                'venue' => $booking->venue->venue_name
            ];

            // Send the review link via email
            Mail::to($booking->email)->send(new ReviewLinkMail($data));

        }
        die();

    }
}
