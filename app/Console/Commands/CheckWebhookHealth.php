<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckWebhookHealth extends Command
{
    protected $signature = 'webhook:check';
    protected $description = 'Check if webhook endpoint is accessible and configured correctly';

    public function handle()
    {
        $this->info('🔍 Webhook Health Check');
        $this->newLine();

        // Check configuration
        $webhookSecret = config('services.stripe.webhook_secret');
        $stripeKey = config('services.stripe.secret_key');
        $baseUrl = config('app.url');

        $this->info('📋 Configuration Check:');
        $this->table(
            ['Setting', 'Status', 'Value'],
            [
                ['APP_URL', $baseUrl ? '✅ Set' : '❌ Missing', $baseUrl ?: 'Not set'],
                ['Stripe Webhook Secret', $webhookSecret ? '✅ Set' : '❌ Missing', $webhookSecret ? 'Set (hidden)' : 'Not set'],
                ['Stripe Secret Key', $stripeKey ? '✅ Set' : '❌ Missing', $stripeKey ? 'Set (hidden)' : 'Not set'],
            ]
        );

        $this->newLine();

        // Check webhook endpoint accessibility
        $webhookUrl = rtrim($baseUrl, '/') . '/webhooks/stripe';
        $this->info("🌐 Testing webhook endpoint: {$webhookUrl}");

        try {
            // Make a GET request to the webhook endpoint (should return 405 Method Not Allowed)
            $response = Http::timeout(10)->get($webhookUrl);

            if ($response->status() === 405) {
                $this->info('✅ Webhook endpoint is accessible (405 Method Not Allowed is expected for GET)');
            } elseif ($response->status() === 200) {
                $this->warn('⚠️  Endpoint responds to GET (unexpected - should only accept POST)');
            } else {
                $this->error("❌ Endpoint returned status: {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Cannot reach webhook endpoint: " . $e->getMessage());
            $this->info('   This could be normal if running locally without ngrok/tunneling');
        }

        $this->newLine();
        $this->info('🔧 Stripe Dashboard Configuration:');
        $this->info('• Webhook URL should be: ' . $webhookUrl);
        $this->info('• Required events:');
        $this->info('  - charge.dispute.created');
        $this->info('  - charge.dispute.updated');
        $this->info('  - charge.dispute.closed');
        $this->info('  - All your existing payment events');

        $this->newLine();
        $this->info('🧪 Testing with dispute cards:');
        $this->info('• Run: php artisan dispute:cards');
        $this->info('• Use card 4000000000000259 in your booking form');
        $this->info('• Monitor: tail -f storage/logs/laravel.log');

        if (!$webhookSecret) {
            $this->newLine();
            $this->error('❌ STRIPE_WEBHOOK_SECRET is not set in your .env file!');
            $this->info('   Get this from Stripe Dashboard → Webhooks → Your endpoint → Signing secret');
        }

        return 0;
    }
}
