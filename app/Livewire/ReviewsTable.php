<?php

namespace App\Livewire;

use App\Models\Review;
use App\Models\ReviewReply;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsTable extends Component
{
    use WithPagination;

    public $showReplyModal = false;
    public $selectedReviewId;
    public $selectedReview;
    public $replyText = '';

    protected $listeners = ['reviewDeleted' => '$refresh'];

    public function render()
    {
        $reviews = Review::with(['booking', 'reply'])
            ->latest()
            ->paginate(10);

        return view('livewire.reviews-table', [
            'reviews' => $reviews
        ]);
    }

    public function deleteReview($reviewId)
    {
        $review = Review::find($reviewId);

        if ($review) {
            // Delete associated reply if exists
            if ($review->reply) {
                $review->reply->delete();
            }

            $review->delete();

            session()->flash('message', 'Review deleted successfully.');
            $this->dispatch('reviewDeleted');
        }
    }

    public function openReplyModal($reviewId)
    {
        $this->selectedReviewId = $reviewId;
        $this->selectedReview = Review::find($reviewId);
        $this->replyText = '';

        // Check if reply already exists
        $existingReply = ReviewReply::where('review_id', $reviewId)->first();
        if ($existingReply) {
            $this->replyText = $existingReply->reply;
        }

        $this->showReplyModal = true;
    }

    public function closeReplyModal()
    {
        $this->showReplyModal = false;
        $this->selectedReviewId = null;
        $this->selectedReview = null;
        $this->replyText = '';
    }

    public function saveReply()
    {
        $this->validate([
            'replyText' => 'required|min:10',
        ]);

        ReviewReply::updateOrCreate(
            ['review_id' => $this->selectedReviewId],
            ['reply' => $this->replyText]
        );

        session()->flash('message', 'Reply saved successfully.');
        $this->closeReplyModal();
    }
}
