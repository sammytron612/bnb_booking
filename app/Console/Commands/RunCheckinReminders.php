<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendCheckinReminders;

class RunCheckinReminders extends Command
{
    protected $signature = 'run:checkin-reminders';
    protected $description = 'Run check-in reminders job manually';

    public function handle()
    {
        SendCheckinReminders::dispatch();
        $this->info('Check-in reminders job dispatched successfully!');
    }
}
