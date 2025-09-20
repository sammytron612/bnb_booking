<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendCheckinReminders;
use App\Jobs\SendReviewLinkEmails;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the email jobs to run daily
Schedule::job(new SendCheckinReminders())->dailyAt('09:00')->name('send-checkin-reminders');
Schedule::job(new SendReviewLinkEmails())->dailyAt('10:00')->name('send-review-link-emails');
