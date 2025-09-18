<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
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

        // Extract venue from URL paths like 'light-house' or 'saras'
        if ($path === 'light-house') {
            return 'light-house';
        } elseif ($path === 'saras') {
            return 'saras';
        }

        return null;
    }

    private function loadReviews()
    {
        if ($this->venue) {
            // Get total count of reviews for this venue
            $this->totalReviews = Review::whereHas('booking', function($query) {
                $query->where('venue', $this->venue);
            })->count();

            // Get limited reviews for this venue through the booking relationship
            $this->reviews = Review::whereHas('booking', function($query) {
                $query->where('venue', $this->venue);
            })->with('booking')->orderBy('created_at', 'desc')->limit($this->reviewsToShow)->get();
        } else {
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
