<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupAbandonedBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:cleanup-abandoned
                            {--dry-run : Show what would be cleaned up without actually doing it}
                            {--hours=12 : Hours after creation to consider booking abandoned}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up abandoned bookings that have not been paid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $hoursOld = (int) $this->option('hours');

        $cutoffTime = Carbon::now()->subHours($hoursOld);
        // Both pending and payment_failed use same timeframe - no extra grace period needed

        $this->info("Looking for abandoned bookings...");
        $this->info("- Pending bookings older than {$hoursOld} hours (webhook failures)");
        $this->info("- Payment_failed bookings older than {$hoursOld} hours (payment failures)");

        // Find bookings that should be cleaned up
        // This catches webhook failures (pending) and payment failures (payment_failed)
        // Note: payment_expired no longer used - session expired goes directly to abandoned
        $abandonedBookings = Booking::where('is_paid', false)
            ->where(function ($query) use ($cutoffTime) {
                $query->where(function ($q) use ($cutoffTime) {
                    // Webhook failures: pending bookings older than specified hours
                    $q->where('status', 'pending')
                      ->where('created_at', '<', $cutoffTime);
                })
                ->orWhere(function ($q) use ($cutoffTime) {
                    // Payment failures: payment_failed bookings older than specified hours
                    $q->where('status', 'payment_failed')
                      ->where('updated_at', '<', $cutoffTime);
                });
            })
            ->get();

        if ($abandonedBookings->isEmpty()) {
            $this->info('✅ No abandoned bookings found.');
            return 0;
        }

        $this->info("Found {$abandonedBookings->count()} abandoned booking(s):");

        foreach ($abandonedBookings as $booking) {
            $ageInHours = $booking->created_at->diffInHours(now());

            $this->line("  - {$booking->getDisplayBookingId()} ({$booking->venue->venue_name}) - {$ageInHours}h old");
            $this->line("    📧 {$booking->email} | 📅 {$booking->date_range} | 💰 {$booking->formatted_total}");

            if (!$dryRun) {
                try {
                    // Update booking status to abandoned instead of deleting
                    $booking->update([
                        'status' => 'abandoned',
                        'notes' => ($booking->notes ? $booking->notes . "\n" : '') .
                                  "Automatically marked as abandoned on " . now()->format('Y-m-d H:i:s') .
                                  " (was unpaid for {$ageInHours} hours)"
                    ]);

                    Log::info('Booking marked as abandoned', [
                        'booking_id' => $booking->getBookingReference(),
                        'booking_display_id' => $booking->getDisplayBookingId(),
                        'age_hours' => $ageInHours,
                        'email' => $booking->email,
                        'total_price' => $booking->total_price
                    ]);

                    $this->line("    ✅ Marked as abandoned");

                } catch (\Exception $e) {
                    $this->error("    ❌ Failed to update booking: " . $e->getMessage());

                    Log::error('Failed to mark booking as abandoned', [
                        'booking_id' => $booking->getBookingReference(),
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                $this->line("    🔍 Would mark as abandoned");
            }
        }

        if ($dryRun) {
            $this->warn("\n🔍 DRY RUN: No changes were made. Remove --dry-run to actually cleanup bookings.");
            $this->info("Command would mark {$abandonedBookings->count()} booking(s) as abandoned.");
        } else {
            $this->info("\n✅ Cleanup completed. Processed {$abandonedBookings->count()} abandoned booking(s).");
            $this->info("💡 Tip: Schedule this command to run hourly with: * * * * * php artisan bookings:cleanup-abandoned");
        }

        return 0;
    }
}
