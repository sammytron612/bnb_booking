<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendReviewLinkEmails;

class RunReviewEmails extends Command
{
    protected $signature = 'run:review-emails';
    protected $description = 'Run review link emails job manually';

    public function handle()
    {
        SendReviewLinkEmails::dispatch();
        $this->info('Review link emails job dispatched successfully!');
    }
}
