<!-- Booking Modal -->
    <div
        id="bookingModal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4"
    >
        <div class="relative bg-white dark:bg-gray-800 rounded-xl max-w-6xl max-h-[90vh] w-full overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Availability & Booking</h2>
                <button
                    id="closeBookingModal"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <!-- Pricing Summary -->
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                        £120<span class="text-lg font-normal text-gray-600 dark:text-gray-300">/night</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">
                        Minimum 2-night stay • All amenities included
                    </p>
                </div>

                <!-- Calendar and Booking Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Calendar -->
                    <div class="lg:col-span-2">
                        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 px-4 py-3">
                                <button id="prevMonth" class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Previous month">‹</button>
                                <h3 id="calendarTitle" class="font-semibold text-gray-900 dark:text-white"></h3>
                                <button id="nextMonth" class="px-3 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600" aria-label="Next month">›</button>
                            </div>
                            <div class="grid grid-cols-7 text-center text-xs font-medium text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                                <div class="py-2">Sun</div>
                                <div class="py-2">Mon</div>
                                <div class="py-2">Tue</div>
                                <div class="py-2">Wed</div>
                                <div class="py-2">Thu</div>
                                <div class="py-2">Fri</div>
                                <div class="py-2">Sat</div>
                            </div>
                            <div id="daysGrid" class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-600"></div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Select check-in and check-out dates. Minimum stay: 2 nights. Unavailable dates are disabled.
                        </p>
                    </div>

                    <!-- Booking Summary Sidebar -->
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
                </div>

                <!-- Terms and Cancellation -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-2">Cancellation Policy</h5>
                            <p>Free cancellation up to 48 hours before check-in. After that, 50% refund if cancelled at least 24 hours before check-in.</p>
                        </div>
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white mb-2">House Rules</h5>
                            <ul class="space-y-1">
                                <li>• Check-in: 3:00 PM - 8:00 PM</li>
                                <li>• Check-out: 11:00 AM</li>
                                <li>• No smoking inside</li>
                                <li>• Maximum 6 guests</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
