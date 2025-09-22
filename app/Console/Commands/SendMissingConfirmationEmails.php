<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingConfirmation;
use App\Mail\NewBooking;
use Illuminate\Support\Facades\Log;

class SendMissingConfirmationEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-missing-confirmations {--booking-id= : Specific booking ID to send confirmation for} {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send confirmation emails for paid bookings that are missing them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->option('booking-id');
        $dryRun = $this->option('dry-run');

        if ($bookingId) {
            // Send for specific booking
            $booking = Booking::with('venue')->find($bookingId);

            if (!$booking) {
                $this->error("Booking with ID {$bookingId} not found.");
                return 1;
            }

            $bookings = collect([$booking]);
        } else {
            // Find all paid bookings without confirmation emails sent
            $bookings = Booking::with('venue')
                ->where('is_paid', true)
                ->whereNull('confirmation_email_sent')
                ->get();
        }

        if ($bookings->isEmpty()) {
            $this->info('No bookings found that need confirmation emails.');
            return 0;
        }

        $this->info("Found {$bookings->count()} booking(s) that need confirmation emails:");

        foreach ($bookings as $booking) {
            $this->line("- Booking {$booking->getDisplayBookingId()} ({$booking->name} - {$booking->email})");
        }

        if ($dryRun) {
            $this->info("\n[DRY RUN] No emails will be sent. Remove --dry-run to actually send emails.");
            return 0;
        }

        if (!$this->confirm('Send confirmation emails for these bookings?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Mark as sent first to prevent duplicates
                $booking->update(['confirmation_email_sent' => now()]);

                // Send confirmation email to customer
                Mail::to($booking->email)->send(new BookingConfirmation($booking));

                // Send notification to owner
                if (config('mail.owner_email')) {
                    Mail::to(config('mail.owner_email'))->send(new NewBooking($booking));
                }

                $this->info("✓ Sent confirmation email for booking {$booking->getDisplayBookingId()}");

                Log::info('Manual confirmation email sent', [
                    'booking_id' => $booking->id,
                    'email' => $booking->email,
                    'sent_via' => 'artisan_command'
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to send email for booking {$booking->getDisplayBookingId()}: {$e->getMessage()}");

                Log::error('Failed to send manual confirmation email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);

                $errorCount++;
            }
        }

        $this->info("\nCompleted: {$successCount} sent, {$errorCount} failed");
        return $errorCount > 0 ? 1 : 0;
    }
}
