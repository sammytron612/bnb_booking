<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Reviews extends Component
{
    public $venue;
    public $reviews;
    public $reviewsToShow = 5;
    public $totalReviews = 0;

    public function mount()
    {
        // Get the venue from the current URL
        $this->venue = $this->getVenueFromUrl();

        // Load reviews for this venue
        $this->loadReviews();
    }

    private function getVenueFromUrl()
    {
        $path = request()->path();

        // Map URL paths to database venue names
        if ($path === 'light-house') {
            return 'The Light House';
        } elseif ($path === 'saras') {
            return 'Saras';
        }

        // Also try other possible venue names
        if (str_contains($path, 'lighthouse') || str_contains($path, 'light')) {
            return 'The Light House';
        }

        if (str_contains($path, 'sara')) {
            return 'Saras';
        }

        return null;
    }

    private function loadReviews()
    {
        if ($this->venue) {

            try {
                // Debug: Log the venue we're looking for
                logger('Looking for reviews for venue: ' . $this->venue);

                // Get total count of reviews for this venue
                $this->totalReviews = Review::whereHas('booking', function($query) {
                    $query->where('venue', $this->venue);
                })->count();

                logger('Found ' . $this->totalReviews . ' reviews for venue: ' . $this->venue);

                // Get limited reviews for this venue through the booking relationship
                $this->reviews = Review::whereHas('booking', function($query) {
                    $query->where('venue', $this->venue);
                })->with(['booking', 'reply'])->orderBy('created_at', 'desc')->limit($this->reviewsToShow)->get();

            } catch (\Exception $e) {
                // If relationship fails, fall back to getting all reviews
                logger('Reviews relationship error: ' . $e->getMessage());
                $this->reviews = Review::with(['booking', 'reply'])->orderBy('created_at', 'desc')->limit($this->reviewsToShow)->get();
                $this->totalReviews = Review::count();
            }
        } else {
            logger('No venue found from URL: ' . request()->path());
            $this->reviews = collect();
            $this->totalReviews = 0;
        }

    }

    public function loadMore()
    {
        $this->reviewsToShow += 5;
        $this->loadReviews();
    }

    public function render()
    {
        return view('livewire.reviews', [
            'reviews' => $this->reviews,
            'venue' => $this->venue
        ]);
    }
}
