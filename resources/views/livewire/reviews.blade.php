<div class="reviews-section">
    @if($venueId)
        @php
            $venueName = $reviews->count() > 0 && $reviews->first()->booking?->venue ?
                         $reviews->first()->booking->venue->venue_name :
                         ($venueId == 1 ? 'The Light House' : 'Saras');
        @endphp
        <h3 class="text-xl font-semibold mb-4 text-center">Reviews for {{ $venueName }}</h3>

        @if($reviews && $reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="bg-white rounded-lg shadow p-6 border">
                        <div class="flex items-center mb-3">
                            <h4 class="font-semibold text-lg mr-4">{{ $review->name }}</h4>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endif
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">({{ $review->rating }}/5)</span>
                            </div>
                        </div>

                        <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>

                        @if($review->booking)
                            <div class="mt-3 text-sm text-gray-500">
                                Stay: {{ $review->booking->date_range }}
                            </div>
                        @endif

                        <div class="mt-2 text-xs text-gray-400">
                            Reviewed on {{ $review->created_at->format('M j, Y') }}
                        </div>

                        @if($review->reply)
                            <div class="mt-4 pl-4 border-l-4 border-blue-200 bg-blue-50 rounded-r-lg p-3">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-800">Management Response</span>
                                </div>
                                <p class="text-sm text-blue-700 leading-relaxed">{{ $review->reply->reply }}</p>
                                <div class="mt-2 text-xs text-blue-500">
                                    Replied on {{ $review->reply->created_at->format('M j, Y') }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Load More Button -->
            @if($reviews->count() < $totalReviews)
                <div class="text-center mt-6">
                    <button
                        wire:click="loadMore"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors inline-flex items-center"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="loadMore">
                            Load More Reviews
                        </span>
                        <span wire:loading wire:target="loadMore" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>
                </div>
            @endif

            <!-- Reviews Counter -->
            @if($totalReviews > 0)
                <div class="text-center mt-4 text-sm text-gray-500">
                    Showing {{ $reviews->count() }} of {{ $totalReviews }} reviews
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <div class="text-gray-500 mb-2">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l2.595-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                    </svg>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-1">No reviews yet</h4>
                <p class="text-gray-500">Be the first to leave a review for {{ $venueName }}!</p>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <p class="text-gray-500">Unable to determine venue from URL.</p>
        </div>
    @endif
</div>
