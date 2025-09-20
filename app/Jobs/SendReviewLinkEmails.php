<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Mail\ReviewLink as ReviewLinkMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendReviewLinkEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get bookings that checked out within the last 24 hours, don't have reviews, and haven't been sent review links
        $bookings = Booking::with('venue')
            ->where('check_out', '<', now()->addHours(24))
            ->whereDoesntHave('reviews')
            ->whereNull('review_link')
            ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach($bookings as $booking) {
            try {
                // Generate a signed review link that expires in 96 hours
                $reviewLink = URL::temporarySignedRoute(
                    'reviews.create',
                    now()->addHours(96),
                    ['booking' => $booking->id]
                );

                // Create the data array
                $data = [
                    'reviewLink' => $reviewLink,
                    'booking_id' => $booking->id,
                    'name' => $booking->name,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ];

                // Send the review link via email
                Mail::to($booking->email)->send(new ReviewLinkMail($data));

                // Update the review_link field to mark that the review link has been sent
                $booking->update(['review_link' => now()]);

                Log::info("Review link email sent successfully", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send review link email", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);

                $errorCount++;
            }
        }

        Log::info("Review link emails job completed", [
            'total_bookings' => $bookings->count(),
            'successful_emails' => $successCount,
            'failed_emails' => $errorCount
        ]);
    }
}
