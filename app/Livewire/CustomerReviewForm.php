<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use App\Models\Booking;

class CustomerReviewForm extends Component
{
    public $booking;
    public $name = '';
    public $email = '';
    public $rating = 0; // Start with no rating selected
    public $review = '';
    public $submitted = false;

    public function mount($booking)
    {
        // Get the booking data
        $this->booking = Booking::find($booking);

        if ($this->booking) {
            // Pre-fill with booking information
            $this->name = $this->booking->name;
            $this->email = $this->booking->email;
        }
    }    protected $rules = [
        'name' => 'required|min:2|max:255',
        'email' => 'required|email|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'required|min:10|max:2000',
    ];

    protected $messages = [
        'name.required' => 'Please enter your name.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'rating.required' => 'Please select a rating by clicking on the stars.',
        'rating.min' => 'Please schoose a rating.',
        'rating.max' => 'Rating cannot exceed 5 stars.',
        'review.required' => 'Please write a review.',
        'review.min' => 'Review must be at least 10 characters long.',
        'review.max' => 'Review cannot exceed 2000 characters.',
    ];

    public function submitReview()
    {
        $this->validate();

        // Check if booking exists and hasn't already been reviewed
        if (!$this->booking) {
            session()->flash('error', 'Invalid booking information.');
            return;
        }

        // Check if review already exists for this booking
        $existingReview = Review::where('booking_id', $this->booking->id)->first();
        if ($existingReview) {
            session()->flash('error', 'A review has already been submitted for this booking.');
            return;
        }

        // Create the review
        Review::create([
            'name' => $this->name,
            'review' => $this->review,
            'rating' => $this->rating,
            'booking_id' => $this->booking->id,
        ]);

        $this->submitted = true;
        session()->flash('success', 'Thank you for your review! Your feedback helps future guests.');
    }

    public function setRating($rating)
    {
        // Validate rating is within acceptable range
        if ($rating >= 1 && $rating <= 5) {
            $this->rating = (int) $rating;

            // Clear any existing rating validation errors
            $this->resetErrorBag('rating');
        }
    }

    public function getRatingDescriptionProperty()
    {
        return match($this->rating) {
            1 => 'Poor',
            2 => 'Fair',
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent',
            default => 'Click to rate'
        };
    }

    public function render()
    {
        return view('livewire.customer-review-form');
    }
}
