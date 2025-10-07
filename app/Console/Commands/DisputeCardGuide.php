<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DisputeCardGuide extends Command
{
    protected $signature = 'dispute:cards';
    protected $description = 'Show Stripe test cards that trigger automatic disputes';

    public function handle()
    {
        $this->info('🃏 Stripe Test Cards for Dispute Testing');
        $this->newLine();

        $this->info('Use these cards in your booking form to trigger automatic disputes:');
        $this->newLine();

        $this->table(
            ['Card Number', 'Dispute Type', 'When Dispute Occurs'],
            [
                ['4000000000000259', 'fraudulent', 'Immediately after payment'],
                ['4000000000009995', 'unrecognized', 'Immediately after payment'],
                ['4000000000000077', 'subscription_canceled', 'Immediately after payment'],
                ['4000000000000002', 'N/A - Declined', 'Payment will fail'],
            ]
        );

        $this->newLine();
        $this->info('📋 Testing Steps:');
        $this->info('1. Go to your booking form in a browser');
        $this->info('2. Fill out booking details');
        $this->info('3. Use card: 4000000000000259');
        $this->info('4. Use any future expiry date (e.g., 12/26)');
        $this->info('5. Use any 3-digit CVC (e.g., 123)');
        $this->info('6. Complete the booking');
        $this->info('7. Payment will succeed initially');
        $this->info('8. Stripe will create a dispute automatically');
        $this->info('9. Your webhook will fire and email will be sent!');

        $this->newLine();
        $this->info('🔍 Monitor the process:');
        $this->info('• Watch logs: tail -f storage/logs/laravel.log');
        $this->info('• Check disputes: php artisan dispute:monitor');
        $this->info('• Check your email for notifications');

        $this->newLine();
        $this->warn('⚠️  The dispute may take a few minutes to appear in Stripe and trigger the webhook.');

        $this->newLine();
        $this->info('💡 Alternative: Use stripe CLI to trigger events instantly:');
        $this->info('   stripe trigger charge.dispute.created');

        return 0;
    }
}
