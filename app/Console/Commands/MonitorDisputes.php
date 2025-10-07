<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookingDispute;
use App\Models\Booking;

class MonitorDisputes extends Command
{
    protected $signature = 'dispute:monitor';
    protected $description = 'Monitor and display current dispute status';

    public function handle()
    {
        $this->info('🛡️  Dispute System Status Monitor');
        $this->newLine();

        // Check total bookings and paid bookings
        $totalBookings = Booking::count();
        $paidBookings = Booking::where('is_paid', true)->count();
        $totalRevenue = Booking::where('is_paid', true)->sum('total_price');

        $this->info("📊 Booking Statistics:");
        $this->table(
            ['Metric', 'Count', 'Value'],
            [
                ['Total Bookings', $totalBookings, '-'],
                ['Paid Bookings', $paidBookings, '-'],
                ['Total Revenue', '-', '£' . number_format((float)$totalRevenue, 2)],
            ]
        );

        // Check disputes
        $totalDisputes = BookingDispute::count();
        $activeDisputes = BookingDispute::whereNotIn('status', ['won', 'lost', 'warning_closed'])->count();
        $urgentDisputes = BookingDispute::whereNotNull('evidence_due_by')
            ->where('evidence_due_by', '<=', now()->addDays(2))
            ->whereNotIn('status', ['won', 'lost', 'warning_closed'])
            ->count();

        $this->newLine();
        $this->info("⚖️  Dispute Statistics:");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Disputes', $totalDisputes],
                ['Active Disputes', $activeDisputes],
                ['🚨 Urgent Disputes (≤2 days)', $urgentDisputes],
            ]
        );

        // Show recent disputes if any
        $recentDisputes = BookingDispute::with('booking')
            ->latest()
            ->take(5)
            ->get();

        if ($recentDisputes->count() > 0) {
            $this->newLine();
            $this->info("📋 Recent Disputes:");

            $disputeData = $recentDisputes->map(function ($dispute) {
                return [
                    'ID' => substr($dispute->stripe_dispute_id, 0, 15) . '...',
                    'Booking' => $dispute->booking->booking_id,
                    'Amount' => $dispute->amount_in_pounds,
                    'Status' => $dispute->friendly_status,
                    'Urgent' => $dispute->is_urgent ? '🚨 YES' : 'No',
                    'Created' => $dispute->created_at->format('Y-m-d H:i'),
                ];
            })->toArray();

            $this->table(
                ['Dispute ID', 'Booking', 'Amount', 'Status', 'Urgent', 'Created'],
                $disputeData
            );
        } else {
            $this->newLine();
            $this->info("✅ No disputes found - system is ready for when they occur!");
        }

        // System health checks
        $this->newLine();
        $this->info("🔧 System Health:");

        $ownerEmail = config('mail.owner_email');
        $webhookSecret = config('services.stripe.webhook_secret') ? '✅ Set' : '❌ Missing';
        $stripeKey = config('services.stripe.secret_key') ? '✅ Set' : '❌ Missing';

        $this->table(
            ['Component', 'Status'],
            [
                ['Owner Email', $ownerEmail ?: '❌ Not configured'],
                ['Webhook Secret', $webhookSecret],
                ['Stripe Secret Key', $stripeKey],
                ['Database Table', '✅ booking_disputes exists'],
            ]
        );

        if (!$ownerEmail) {
            $this->newLine();
            $this->warn("⚠️  Set OWNER_EMAIL in your .env file to receive dispute notifications!");
        }

        $this->newLine();
        $this->info("💡 Use 'php artisan dispute:monitor' anytime to check status");

        return 0;
    }
}
