<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use Illuminate\Http\Request;

class Reviews extends Component
{
    public $venueId;
    public $reviews;
    public $reviewsToShow = 5;
    public $totalReviews = 0;

    public function mount()
    {
        // Get the venue ID from the current URL
        $this->venueId = $this->getVenueIdFromUrl();

        // Load reviews for this venue
        $this->loadReviews();
    }

    private function getVenueIdFromUrl()
    {
        $path = request()->path();

        // Extract route parameter from dynamic venue URL
        if (preg_match('/^venue\/(.+)$/', $path, $matches)) {
            $routeParam = $matches[1];
            $venue = \App\Models\Venue::where('route', $routeParam)->first();
            return $venue ? $venue->id : null;
        }

        // Backward compatibility for old routes
        if ($path === 'light-house') {
            return 1; // The Light House
        } elseif ($path === 'saras') {
            return 2; // Saras
        }

        // Also try other possible venue names
        if (str_contains($path, 'lighthouse') || str_contains($path, 'light')) {
            return 1; // The Light House
        }

        if (str_contains($path, 'sara')) {
            return 2; // Saras
        }

        return null;
    }

    private function loadReviews()
    {
        if ($this->venueId) {

            try {
                // Debug: Log the venue ID we're looking for
                logger('Looking for reviews for venue ID: ' . $this->venueId);

                // Get total count of reviews for this venue
                $this->totalReviews = Review::whereHas('booking', function($query) {
                    $query->where('venue_id', $this->venueId);
                })->count();

                logger('Found ' . $this->totalReviews . ' reviews for venue ID: ' . $this->venueId);

                // Get limited reviews for this venue through the booking relationship
                $this->reviews = Review::whereHas('booking', function($query) {
                    $query->where('venue_id', $this->venueId);
                })->with(['booking.venue', 'reply'])->orderBy('created_at', 'desc')->limit($this->reviewsToShow)->get();

            } catch (\Exception $e) {
                // If relationship fails, fall back to getting all reviews
                logger('Reviews relationship error: ' . $e->getMessage());
                $this->reviews = Review::with(['booking.venue', 'reply'])->orderBy('created_at', 'desc')->limit($this->reviewsToShow)->get();
                $this->totalReviews = Review::count();
            }
        } else {
            logger('No venue ID found from URL: ' . request()->path());
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
            'venueId' => $this->venueId
        ]);
    }
}
