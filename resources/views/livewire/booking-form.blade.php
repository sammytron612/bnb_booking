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
                <button
                    wire:click="submitBooking"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors disabled:opacity-50"
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
        <a href="mailto:booking@seahamretreats.com" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            booking@seahamretreats.com
        </a>
    </div>
</div>
