<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class GenerateBookingIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:generate-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique booking IDs for existing bookings that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating booking IDs for existing bookings...');

        $bookingsWithoutIds = Booking::whereNull('booking_id')->get();

        if ($bookingsWithoutIds->isEmpty()) {
            $this->info('All bookings already have booking IDs.');
            return 0;
        }

        $count = 0;
        foreach ($bookingsWithoutIds as $booking) {
            $booking->generateBookingId();
            $count++;
            $this->line("Generated booking ID for booking #{$booking->id}: BNB-{$booking->booking_id}");
        }

        $this->info("Successfully generated {$count} booking IDs.");
        return 0;
    }
}
