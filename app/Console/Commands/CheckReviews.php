<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Review;
use App\Models\Booking;

class CheckReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the reviews data in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Reviews Data Check ===');

        $totalReviews = Review::count();
        $this->info("Total reviews: {$totalReviews}");

        $lightHouseReviews = Review::whereHas('booking', function($query) {
            $query->where('venue', 'light-house');
        })->count();

        $sarasReviews = Review::whereHas('booking', function($query) {
            $query->where('venue', 'saras');
        })->count();

        $this->info("Light House reviews: {$lightHouseReviews}");
        $this->info("Saras reviews: {$sarasReviews}");

        $this->info("\n=== Sample Reviews ===");

        $sampleLightHouse = Review::whereHas('booking', function($query) {
            $query->where('venue', 'light-house');
        })->with('booking')->first();

        if ($sampleLightHouse) {
            $this->info("Light House sample:");
            $this->line("  Name: {$sampleLightHouse->name}");
            $this->line("  Rating: {$sampleLightHouse->rating}/5");
            $this->line("  Review: " . substr($sampleLightHouse->review, 0, 100) . "...");
        }

        $sampleSaras = Review::whereHas('booking', function($query) {
            $query->where('venue', 'saras');
        })->with('booking')->first();

        if ($sampleSaras) {
            $this->info("\nSaras sample:");
            $this->line("  Name: {$sampleSaras->name}");
            $this->line("  Rating: {$sampleSaras->rating}/5");
            $this->line("  Review: " . substr($sampleSaras->review, 0, 100) . "...");
        }

        return Command::SUCCESS;
    }
}
