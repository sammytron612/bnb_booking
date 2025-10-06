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
        // Get bookings that checked out 1+ days ago (but not too old), don't have reviews, and haven't been sent review links
        $bookings = Booking::with('venue')
            ->where('check_out', '>=', now()->subDays(30)) // Don't send to very old bookings
            ->where('check_out', '<=', now()->subDay())     // Must be at least 1 day after checkout
            ->whereIn('status', ['confirmed'])          // Include partially refunded bookings
            ->where('is_paid', true)                        // Only paid bookings
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
                    'status' => $booking->status,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send review link email", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
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
