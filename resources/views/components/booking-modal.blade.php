<!-- Booking Modal -->
    @props(['price', 'venue'])
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
                        £{{$price}}<span class="text-lg font-normal text-gray-600 dark:text-gray-300">/night</span>
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
                    <livewire:booking-form :pricePerNight="$price" :venue="$venue" />
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
