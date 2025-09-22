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
        // Get bookings with check-in from 2 days ago to today that haven't had reminders sent
        $bookings = Booking::with('venue')
            ->where('check_in', '>=', now()->subDays(3)->format('Y-m-d'))
            ->where('check_in', '<=', now()->format('Y-m-d'))
            ->whereNull('check_in_reminder')
            ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach($bookings as $booking) {
            try {
                Mail::to($booking->email)->send(new CheckinMail($booking));

                // Update the check_in_reminder field to mark that the reminder has been sent
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
