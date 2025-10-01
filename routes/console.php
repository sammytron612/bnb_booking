<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendCheckinReminders;
use App\Jobs\SendReviewLinkEmails;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Manual job runners
Artisan::command('test:checkin', function () {
    SendCheckinReminders::dispatch();
    $this->info('Check-in reminders dispatched!');
})->purpose('Run check-in reminders manually');

Artisan::command('test:reviews', function () {
    SendReviewLinkEmails::dispatch();
    $this->info('Review emails dispatched!');
})->purpose('Run review emails manually');

// Schedule the email jobs to run daily
Schedule::job(new SendCheckinReminders())->dailyAt('09:00')->name('send-checkin-reminders');
Schedule::job(new SendReviewLinkEmails())->dailyAt('11:27')->name('send-review-link-emails');

// Schedule cleanup of abandoned bookings - runs every hour
Schedule::command('bookings:cleanup-abandoned')->hourly()->name('cleanup-abandoned-bookings');
