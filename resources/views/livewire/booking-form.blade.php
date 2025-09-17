<div class="space-y-4">
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <h4 class="font-semibold mb-3 text-gray-900 dark:text-white">Your Selection</h4>
        <div class="space-y-2">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Check-in: <span id="selCheckIn" class="font-medium">—</span>
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Check-out: <span id="selCheckOut" class="font-medium">—</span>
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Nights: <span id="selNights" class="font-medium">0</span>
            </p>
            <div class="border-t border-gray-200 dark:border-gray-600 pt-2 mt-3">
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    Total: <span id="selTotal" class="text-blue-600 dark:text-blue-400">£0</span>
                </p>
            </div>
        </div>

        <!-- Booking Form (appears when dates are selected) -->
        <div id="bookingForm" class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 hidden">
            <h5 class="font-semibold text-green-800 dark:text-green-200 mb-3">Guest Details</h5>
            <form id="guestDetailsForm" class="space-y-3">
                <div>
                    <label for="guestName" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Full Name *
                    </label>
                    <input
                        type="text"
                        id="guestName"
                        name="guestName"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Enter your full name"
                    >
                </div>
                <div>
                    <label for="guestEmail" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email Address *
                    </label>
                    <input
                        type="email"
                        id="guestEmail"
                        name="guestEmail"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Enter your email address"
                    >
                </div>
                <div>
                    <label for="guestPhone" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Phone Number *
                    </label>
                    <input
                        type="tel"
                        id="guestPhone"
                        name="guestPhone"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Enter your phone number"
                    >
                </div>
                <div class="pt-1">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        * Required fields for booking confirmation
                    </p>
                </div>
            </form>
        </div>

        <div class="mt-4 space-y-3">

            <button
                id="clearSelection"
                class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors"
            >
                Clear Selection
            </button>
            <button
                id="proceedBooking"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                disabled
            >
                Proceed to Booking
            </button>
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
