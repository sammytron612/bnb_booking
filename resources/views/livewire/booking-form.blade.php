<div class="space-y-4">
    <!-- Success/Error Messages -->
    @if (session()->has('booking_success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('booking_success') }}
        </div>
    @endif

    @if (session()->has('booking_error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('booking_error') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">Your Selection</h4>
        <div class="space-y-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Check-in: <span class="font-medium">{{ $this->formattedCheckIn }}</span>
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Check-out: <span class="font-medium">{{ $this->formattedCheckOut }}</span>
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Nights: <span class="font-medium">{{ $nights }}</span>
            </p>
            <div class="border-t border-gray-200 dark:border-gray-600 pt-2 mt-3">
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    Total: <span class="text-blue-600 dark:text-blue-400">{{ $this->formattedTotal }}</span>
                </p>
            </div>
        </div>

        <!-- Booking Form (appears when dates are selected) -->
        @if ($this->hasValidDates)
            @if($venue && !$venue->booking_enabled)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-amber-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h6 class="font-semibold text-amber-800 dark:text-amber-200">Booking Temporarily Unavailable</h6>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                    Online booking is currently disabled for this property. Please contact us directly for availability and reservations.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600" wire:transition>
                    <h5 class="font-semibold text-green-800 dark:text-green-200 mb-3">Guest Details</h5>

                <form wire:submit="submitBooking" class="space-y-3">
                    <div>
                        <label for="guestName" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Full Name *
                        </label>
                        <input
                            type="text"
                            id="guestName"
                            wire:model="guestName"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('guestName') border-red-500 @enderror"
                            placeholder="Enter your full name"
                        >
                        @error('guestName')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guestEmail" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Email Address *
                        </label>
                        <input
                            type="email"
                            id="guestEmail"
                            wire:model="guestEmail"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('guestEmail') border-red-500 @enderror"
                            placeholder="Enter your email address"
                        >
                        @error('guestEmail')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guestPhone" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Phone Number *
                        </label>
                        <input
                            type="tel"
                            id="guestPhone"
                            wire:model="guestPhone"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('guestPhone') border-red-500 @enderror"
                            placeholder="Enter your phone number"
                        >
                        @error('guestPhone')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            * Required fields for booking confirmation
                        </p>
                    </div>
                </form>
            </div>
            @endif
        @endif

        <div class="mt-4 space-y-3">
            <button
                wire:click="clearSelection"
                class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Clear Selection</span>
                <span wire:loading>Clearing...</span>
            </button>

            @if ($this->hasValidDates)
                @if($venue && $venue->booking_enabled)
                    <button
                        wire:click="submitBooking"
                        class="w-full bg-blue-600 hover:cursor-pointer hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Book {{ $nights }} Night{{ $nights !== 1 ? 's' : '' }}</span>
                        <span wire:loading>Processing...</span>
                    </button>
                @else
                    <button
                        class="w-full bg-gray-400 text-white font-semibold px-4 py-3 rounded-lg cursor-not-allowed opacity-50"
                        disabled
                    >
                        <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Booking Temporarily Unavailable
                    </button>
                @endif
            @else
                <button
                    class="w-full bg-gray-400 text-white font-semibold px-4 py-3 rounded-lg cursor-not-allowed opacity-50"
                    disabled
                >
                    Select Dates to Book
                </button>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <h4 class="font-semibold mb-2 text-gray-900 dark:text-white">Need Help?</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            Contact us directly for special requests or group bookings.
        </p>
        <a href="mailto:{{config('app.owner_email')}}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            {{config('app.owner_email')}}
        </a>
    </div>
</div>
