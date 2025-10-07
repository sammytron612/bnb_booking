<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingDispute;

class DebugDisputes extends Command
{
    protected $signature = 'dispute:debug';
    protected $description = 'Debug current dispute and booking records';

    public function handle()
    {
        $this->info('🔍 Dispute System Debug');
        $this->newLine();

        // Show recent bookings
        $recentBookings = Booking::where('created_at', '>=', now()->subHours(2))
            ->orderBy('created_at', 'desc')
            ->get(['id', 'booking_id', 'name', 'email', 'stripe_payment_intent_id', 'total_price', 'is_paid', 'created_at']);

        $this->info('📋 Recent Bookings (last 2 hours):');
        if ($recentBookings->count() > 0) {
            $bookingData = $recentBookings->map(function ($booking) {
                return [
                    'DB ID' => $booking->id,
                    'Booking ID' => $booking->booking_id,
                    'Guest' => $booking->name,
                    'Email' => substr($booking->email, 0, 20) . '...',
                    'Payment Intent' => substr($booking->stripe_payment_intent_id ?? 'None', 0, 25) . '...',
                    'Amount' => '£' . $booking->total_price,
                    'Paid' => $booking->is_paid ? 'Yes' : 'No',
                    'Created' => $booking->created_at->format('H:i:s'),
                ];
            })->toArray();

            $this->table(
                ['DB ID', 'Booking ID', 'Guest', 'Email', 'Payment Intent', 'Amount', 'Paid', 'Created'],
                $bookingData
            );
        } else {
            $this->warn('No recent bookings found');
        }

        $this->newLine();

        // Show recent disputes
        $recentDisputes = BookingDispute::with('booking')
            ->where('created_at', '>=', now()->subHours(2))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->info('⚖️ Recent Disputes (last 2 hours):');
        if ($recentDisputes->count() > 0) {
            $disputeData = $recentDisputes->map(function ($dispute) {
                return [
                    'Dispute ID' => substr($dispute->stripe_dispute_id, 0, 20) . '...',
                    'Linked Booking DB ID' => $dispute->booking_id,
                    'Linked Booking ID' => $dispute->booking->booking_id ?? 'Missing',
                    'Linked Guest' => $dispute->booking->name ?? 'Missing',
                    'Amount' => $dispute->amount_in_pounds,
                    'Created' => $dispute->created_at->format('H:i:s'),
                ];
            })->toArray();

            $this->table(
                ['Dispute ID', 'Linked Booking DB ID', 'Linked Booking ID', 'Linked Guest', 'Amount', 'Created'],
                $disputeData
            );
        } else {
            $this->warn('No recent disputes found');
        }

        return 0;
    }
}