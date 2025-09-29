<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\URL;
use App\Models\Booking;
use App\Mail\ReviewLink as ReviewLinkMail;
use App\Mail\CheckinMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ReviewLink extends Controller
{
    public function create()
    {


        //test - Get bookings with check-in 2 days from now
        $bookings = Booking::with('venue')
            ->where('check_in', '=', now()->addDays(2)->format('Y-m-d'))
            ->whereNull('check_in_reminder')
            ->get();

            //dd($bookings);

        // Send check-in email (CheckinMail takes the booking model directly)
        foreach($bookings as $booking)
        {
                try {

                Mail::to($booking->email)->send(new CheckinMail($booking));

                // Update the check_in_reminder field to mark that the reminder has been sent
                $booking->update(['check_in_reminder' => now()]);

                Log::info("Check-in email sent successfully", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ]);
            } catch (\Exception $e) {
                    Log::error("Failed to send check-in email", [
                        'email' => $booking->email,
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }

        }
        //dd('Check-in emails processed   ' . $bookings->count());

        //USE THIS TO GENERATE REVIEW LINKS AND EMAIL TO USERS AS A JOB
        $bookings = Booking::with('venue')
            ->where('check_out', '<', now()->addHours(24))
            ->whereDoesntHave('reviews')
            ->whereNull('review_link')
            ->get();

        //dd($bookings);

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
                'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
            ];

            // Send the review link via email
            try {
                Mail::to($booking->email)->send(new ReviewLinkMail($data));

                // Update the review_link field to mark that the review link has been sent
                $booking->update(['review_link' => now()]);

                Log::info("Review link email sent successfully", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send review link email", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

        }

        die();

    }

    /**
     * Test the email jobs manually
     */
    public function testJobs()
    {
        // Dispatch the jobs for testing
        \App\Jobs\SendCheckinReminders::dispatch();
        \App\Jobs\SendReviewLinkEmails::dispatch();

        return response()->json([
            'message' => 'Email jobs dispatched successfully',
            'jobs' => [
                'SendCheckinReminders' => 'Dispatched',
                'SendReviewLinkEmails' => 'Dispatched'
            ]
        ]);
    }
}
