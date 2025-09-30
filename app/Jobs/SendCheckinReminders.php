<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use App\Mail\CheckinMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCheckinReminders implements ShouldQueue
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
        // Get bookings where check-in is from today to 3 days ahead
        // So if today is Sept 24th, find check-ins from Sept 24th to Sept 27th
        $bookings = Booking::with('venue')
            ->where('check_in', '>=', now()->format('Y-m-d'))             // Today
            ->where('check_in', '<=', now()->addDays(3)->format('Y-m-d')) // 3 days ahead
            ->whereNull('check_in_reminder')
            ->where('status', 'confirmed')                                // Only confirmed bookings
            ->where('is_paid', true)                                      // Only paid bookings
            ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach($bookings as $booking) {
            try {
                Mail::to($booking->email)->send(new CheckinMail($booking));

                // Mark reminder as sent to prevent duplicate emails
                $booking->update(['check_in_reminder' => now()]);

                Log::info("Check-in email sent successfully", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'venue' => $booking->venue ? $booking->venue->venue_name : 'Unknown Venue'
                ]);

                $successCount++;
            } catch (\Exception $e) {
                Log::error("Failed to send check-in email", [
                    'email' => $booking->email,
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);

                $errorCount++;
            }
        }

        Log::info("Check-in reminders job completed", [
            'total_bookings' => $bookings->count(),
            'successful_emails' => $successCount,
            'failed_emails' => $errorCount
        ]);
    }
}
