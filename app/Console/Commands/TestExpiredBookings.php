<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Venue;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:expired-bookings
                            {--create : Create a test booking that appears expired}
                            {--hours=25 : How many hours old to make the test booking}';

    /**
     * The console command description.
     */
    protected $description = 'Create test expired bookings for testing the cleanup functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('create')) {
            $this->createTestBooking();
        } else {
            $this->showTestInstructions();
        }

        return 0;
    }

    private function createTestBooking()
    {
        $hours = (int) $this->option('hours');

        // Get first venue
        $venue = Venue::first();
        if (!$venue) {
            $this->error('No venues found. Please create a venue first.');
            return;
        }

        // Create test booking
        $booking = Booking::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'check_in' => Carbon::tomorrow(),
            'check_out' => Carbon::tomorrow()->addDays(2),
            'venue_id' => $venue->id,
            'nights' => 2,
            'total_price' => 150.00,
            'status' => 'pending',
            'notes' => 'Test booking for expired booking functionality',
        ]);

        // Make it appear old
        $booking->update([
            'created_at' => Carbon::now()->subHours($hours)
        ]);

        $this->info("✅ Created test booking {$booking->getDisplayBookingId()}");
        $this->line("   📧 Email: {$booking->email}");
        $this->line("   📅 Dates: {$booking->date_range}");
        $this->line("   ⏰ Created: {$hours} hours ago");
        $this->line("   🏷️  Status: {$booking->status}");

        $this->warn("\n🧪 Now you can test:");
        $this->line("1. Run: php artisan bookings:cleanup-abandoned --dry-run");
        $this->line("2. Run: php artisan bookings:cleanup-abandoned");
        $this->line("3. Check the booking status changed to 'abandoned'");
    }

    private function showTestInstructions()
    {
        $this->info("🧪 Testing Expired Bookings");
        $this->line("");
        $this->line("Choose a testing method:");
        $this->line("");
        $this->line("1. **Create test booking:**");
        $this->line("   php artisan test:expired-bookings --create");
        $this->line("");
        $this->line("2. **Manual database method:**");
        $this->line("   - Find a pending booking ID");
        $this->line("   - UPDATE bookings SET created_at = DATE_SUB(NOW(), INTERVAL 25 HOUR) WHERE id = [booking_id];");
        $this->line("");
        $this->line("3. **Test with real Stripe (advanced):**");
        $this->line("   - Use Stripe CLI: stripe listen --forward-to localhost:8000/stripe/webhook");
        $this->line("   - Create real booking and wait/simulate session expiry");
        $this->line("");
        $this->line("After creating expired booking, test cleanup:");
        $this->line("• php artisan bookings:cleanup-abandoned --dry-run");
        $this->line("• php artisan bookings:cleanup-abandoned");
    }
}
