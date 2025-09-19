<div class="max-w-4xl mx-auto">
    @if($submitted)
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4">
                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-green-800 mb-2">Review Submitted Successfully!</h2>
            <p class="text-green-700 mb-4">Thank you for taking the time to share your experience with us.</p>
            <a href="{{ route('home') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                Return to Homepage
            </a>
        </div>
    @else
        <!-- Review Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            @if($booking)
                <!-- Booking Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Your Stay Details</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-blue-700">Property:</span>
                            <span class="text-blue-600">{{ $booking->venue }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Booking ID:</span>
                            <span class="text-blue-600 font-mono">{{ $booking->getDisplayBookingId() }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Check-in:</span>
                            <span class="text-blue-600">{{ $booking->check_in->format('M j, Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-blue-700">Check-out:</span>
                            <span class="text-blue-600">{{ $booking->check_out->format('M j, Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit.prevent="submitReview" class="space-y-6">
                <!-- Personal Information -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" id="name" wire:model="name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter your full name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" id="email" wire:model="email" readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
                               placeholder="Enter your email">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rating Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Overall Rating *</label>
                    <p class="text-sm text-gray-500 mb-4">Click on a star to rate your experience</p>
                    <div class="flex items-center space-x-1" x-data="{ hoveredRating: 0 }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button"
                                    wire:click="setRating({{ $i }})"
                                    @mouseenter="hoveredRating = {{ $i }}"
                                    @mouseleave="hoveredRating = 0"
                                    class="focus:outline-none transition-all duration-200 transform hover:scale-110 group">
                                <svg class="w-10 h-10 transition-colors duration-200"
                                     :class="{
                                        'text-yellow-400': {{ $i }} <= (hoveredRating || {{ $rating ?? 0 }}),
                                        'text-gray-300': {{ $i }} > (hoveredRating || {{ $rating ?? 0 }})
                                     }"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                        <div class="ml-4 flex flex-col">
                            <span class="text-lg font-medium text-gray-700">
                                @if($rating > 0)
                                    {{ $rating }} out of 5 stars
                                @else
                                    Please select a rating
                                @endif
                            </span>
                            <span class="text-sm font-medium {{ $rating > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                @if($rating == 1) Poor
                                @elseif($rating == 2) Fair
                                @elseif($rating == 3) Good
                                @elseif($rating == 4) Very Good
                                @elseif($rating == 5) Excellent
                                @else Click on a star to rate
                                @endif
                            </span>
                        </div>
                    </div>
                    @error('rating')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Text -->
                <div>
                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                    <textarea id="review" wire:model="review" rows="6"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                              placeholder="Share your experience with future guests. What did you enjoy most about your stay?"></textarea>
                    <div class="flex justify-between mt-2">
                        @error('review')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="text-sm text-gray-500">Minimum 10 characters required</p>
                        @enderror
                        <p class="text-sm text-gray-500">{{ strlen($review) }}/2000 characters</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('home') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed">
                        <span wire:loading.remove>Submit Review</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>
