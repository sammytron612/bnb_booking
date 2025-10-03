<!-- 14-Day Calendar -->
<div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100 relative z-[60]">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 space-y-3 sm:space-y-0">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
            </svg>
            <span class="hidden sm:inline">
                @if($calendarOffset === 0)
                    Next 14 Days Overview
                @elseif($calendarOffset > 0)
                    Days {{ $calendarOffset + 1 }}-{{ $calendarOffset + 14 }} Overview
                @else
                    Days {{ abs($calendarOffset) - 13 }}-{{ abs($calendarOffset) }} Ago
                @endif
            </span>
            <span class="sm:hidden">
                @if($calendarOffset === 0)
                    Next 14 Days
                @elseif($calendarOffset > 0)
                    Days {{ $calendarOffset + 1 }}-{{ $calendarOffset + 14 }}
                @else
                    Days {{ abs($calendarOffset) - 13 }}-{{ abs($calendarOffset) }}
                @endif
            </span>
        </h3>

        <!-- Navigation Buttons -->
        <div class="flex flex-wrap gap-2 justify-center sm:justify-end">
            <button
                wire:click="navigateCalendar('prev')"
                class="hover:cursor-pointer inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 shadow-sm text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="hidden sm:inline">Previous</span>
                <span class="sm:hidden">Prev</span>
            </button>

            @if($calendarOffset !== 0)
                <button
                    wire:click="$set('calendarOffset', 0)"
                    class="hover:cursor-pointer inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 border border-blue-300 shadow-sm text-xs sm:text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Today
                </button>
            @endif

            <button
                wire:click="navigateCalendar('next')"
                class="hover:cursor-pointer inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 border border-gray-300 shadow-sm text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <span class="hidden sm:inline">Next</span>
                <span class="sm:hidden">Next</span>
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Venue Filtering Buttons -->
    <div class="mb-4 border-t border-gray-200 pt-4">
        <div class="flex flex-wrap gap-2 justify-center">
            <!-- All Venues Button -->
            <button
                wire:click="selectVenue(null)"
                class="inline-flex items-center px-3 py-2 border text-sm font-medium rounded-md transition-colors duration-150 {{ $selectedVenueId === null ? 'border-blue-500 text-blue-700 bg-blue-50 hover:bg-blue-100' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                All Venues
            </button>

            <!-- Individual Venue Buttons -->
            @foreach($availableVenues as $venue)
                <button
                    wire:click="selectVenue({{ $venue->id }})"
                    class="inline-flex items-center px-3 py-2 border text-sm font-medium rounded-md transition-colors duration-150 {{ $selectedVenueId == $venue->id ? 'border-blue-500 text-blue-700 bg-blue-50 hover:bg-blue-100' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                    {{ $venue->venue_name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- First Week (Days 0-6) -->
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-4">
        @foreach($calendarData->take(7) as $day)
            <div class="text-center">
                <!-- Day header -->
                <div class="text-xs font-medium {{ $day['is_today'] ? 'text-blue-600' : 'text-gray-500' }} mb-1 uppercase tracking-wide">
                    {{ $day['date']->format('D') }}
                </div>
                <div class="text-sm font-bold {{ $day['is_today'] ? 'text-blue-700' : 'text-gray-900' }} mb-2">
                    {{ $day['date']->format('M j') }}
                </div>

                <!-- Booking indicator -->
                <div class="relative">
                    @if($day['booking_count'] > 0)
                   <div class="relative hover:z-[70] {{ $day['has_double_booking'] ? 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700' : 'bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700' }} transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                             title="{{ $day['booking_count'] }} booking(s){{ $day['has_double_booking'] ? ' - DOUBLE BOOKING!' : '' }}"
                             data-tooltip-id="tooltip-week1-{{ $loop->index }}">
                            <div class="text-white text-xs font-bold">
                                {{ $day['booking_count'] }}
                                @if($day['has_double_booking'])
                                    <span class="ml-1">⚠</span>
                                @endif
                            </div>
                            <div class="{{ $day['has_double_booking'] ? 'text-red-100' : 'text-blue-100' }} text-xs">
                                {{ $day['booking_count'] === 1 ? 'booking' : 'bookings' }}
                                @if($day['has_double_booking'])
                                    <div class="text-red-200 font-bold text-xs mt-1">CONFLICT!</div>
                                @endif
                            </div>

                            <!-- Check-in indicator -->
                            @if($day['check_in_count'] > 0)
                                <div class="absolute -top-2 -left-2 bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↓{{ $day['check_in_count'] }}
                                </div>
                            @endif

                            <!-- Check-out indicator -->
                            @if($day['check_out_count'] > 0)
                                <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↑{{ $day['check_out_count'] }}
                                </div>
                            @endif

                            <!-- Enhanced tooltip with booking details (First week: below card, above second week) -->
                            <div id="tooltip-week1-{{ $loop->index }}" class="mobile-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                    {{ $day['date']->format('l, F j, Y') }}
                                    @if($day['has_double_booking'])
                                        <span class="text-red-400 ml-2">⚠ DOUBLE BOOKING</span>
                                    @endif
                                </div>

                                @if($day['has_double_booking'])
                                    <div class="mb-3 p-2 bg-red-900 rounded border-l-2 border-red-400">
                                        <div class="font-bold text-red-300">⚠ BOOKING CONFLICTS DETECTED</div>
                                        @foreach($day['double_booking_venues'] as $conflict)
                                            <div class="text-red-200 text-xs mt-1">
                                                {{ $conflict['venue_name'] }}: {{ $conflict['booking_count'] }} overlapping bookings
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($day['check_in_count'] > 0)
                                    <div class="mb-2 p-2 bg-green-800 rounded border-l-2 border-green-400">
                                        <div class="font-medium text-green-300">✓ {{ $day['check_in_count'] }} Check-in{{ $day['check_in_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_ins'] as $checkin)
                                            <div class="text-green-200 text-xs mt-1">{{ $checkin->name }} - {{ $checkin->venue->venue_name }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($day['check_out_count'] > 0)
                                    <div class="mb-2 p-2 bg-red-800 rounded border-l-2 border-red-400">
                                        <div class="font-medium text-red-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_outs'] as $checkout)
                                            <div class="text-red-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
                                        @endforeach
                                    </div>
                                @endif
                                @foreach($day['bookings'] as $booking)
                                    <div class="mb-2 p-2 bg-gray-800 rounded border-l-2 border-blue-400">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-medium text-white">{{ $booking->name }}</div>
                                                <div class="text-gray-300 text-xs">{{ $booking->venue->venue_name }}</div>
                                            </div>
                                            <div class="font-mono text-xs text-blue-300">{{ $booking->getDisplayBookingId() }}</div>
                                        </div>
                                        <div class="text-gray-400 text-xs mt-1">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('M j') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M j') }}
                                            ({{ $booking->nights }} {{ $booking->nights === 1 ? 'night' : 'nights' }})
                                        </div>
                                        <div class="text-green-400 text-xs mt-1 font-medium">
                                            £{{ number_format($booking->total_price, 2) }}
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Arrow pointing up (connects to card above) -->
                                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2">
                                    <div class="border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($day['check_out_count'] > 0)
                            <!-- Show blank card with checkout icon when no bookings but checkouts exist -->
                            <div class="group relative bg-gray-100 hover:bg-gray-200 transition-colors duration-200 rounded-lg p-3 h-16 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 mobile-tooltip-trigger"
                                 title="{{ $day['check_out_count'] }} checkout(s)"
                                 data-tooltip-id="checkout-tooltip-week1-{{ $loop->index }}">
                                <div class="text-gray-400 text-xs font-medium">No bookings</div>

                                <!-- Check-out indicator -->
                                <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↑{{ $day['check_out_count'] }}
                                </div>

                                <!-- Enhanced tooltip with checkout details (First week: below card) -->
                                <div id="checkout-tooltip-week1-{{ $loop->index }}" class="mobile-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                    <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                        {{ $day['date']->format('l, F j, Y') }}
                                        @if($day['has_double_booking'])
                                            <span class="text-red-400 ml-2">⚠ DOUBLE BOOKING</span>
                                        @endif
                                    </div>

                                    @if($day['has_double_booking'])
                                        <div class="mb-3 p-2 bg-red-900 rounded border-l-2 border-red-400">
                                            <div class="font-bold text-red-300">⚠ BOOKING CONFLICTS DETECTED</div>
                                            @foreach($day['double_booking_venues'] as $conflict)
                                                <div class="text-red-200 text-xs mt-1">
                                                    {{ $conflict['venue_name'] }}: {{ $conflict['booking_count'] }} overlapping bookings
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mb-2 p-2 bg-red-800 rounded border-l-2 border-red-400">
                                        <div class="font-medium text-red-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_outs'] as $checkout)
                                            <div class="text-red-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
                                        @endforeach
                                    </div>

                                    <!-- Arrow pointing up (connects to card above) -->
                                    <div class="absolute -top-1 left-1/2 transform -translate-x-1/2">
                                        <div class="border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-100 hover:bg-gray-200 transition-colors duration-200 rounded-lg p-3 h-16 flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                                <div class="text-gray-400 text-xs font-medium">No bookings</div>
                            </div>
                        @endif
                    @endif

                    <!-- Today indicator -->
                    @if($day['is_today'])
                        <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Second Week (Days 7-13) -->
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        @foreach($calendarData->skip(7) as $day)
            <div class="text-center">
                <!-- Day header -->
                <div class="text-xs font-medium {{ $day['is_today'] ? 'text-blue-600' : 'text-gray-500' }} mb-1 uppercase tracking-wide">
                    {{ $day['date']->format('D') }}
                </div>
                <div class="text-sm font-bold {{ $day['is_today'] ? 'text-blue-700' : 'text-gray-900' }} mb-2">
                    {{ $day['date']->format('M j') }}
                </div>

                <!-- Booking indicator -->
                <div class="relative">
                    @if($day['booking_count'] > 0)
                   <div class="relative hover:z-[70] {{ $day['has_double_booking'] ? 'bg-gradient-to-br from-red-500 to-red-600 hover:from-red-600 hover:to-red-700' : 'bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700' }} transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                             title="{{ $day['booking_count'] }} booking(s){{ $day['has_double_booking'] ? ' - DOUBLE BOOKING!' : '' }}"
                             data-tooltip-id="tooltip-week2-{{ $loop->index }}">
                            <div class="text-white text-xs font-bold">
                                {{ $day['booking_count'] }}
                                @if($day['has_double_booking'])
                                    <span class="ml-1">⚠</span>
                                @endif
                            </div>
                            <div class="{{ $day['has_double_booking'] ? 'text-red-100' : 'text-blue-100' }} text-xs">
                                {{ $day['booking_count'] === 1 ? 'booking' : 'bookings' }}
                                @if($day['has_double_booking'])
                                    <div class="text-red-200 font-bold text-xs mt-1">CONFLICT!</div>
                                @endif
                            </div>

                            <!-- Check-in indicator -->
                            @if($day['check_in_count'] > 0)
                                <div class="absolute -top-2 -left-2 bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↓{{ $day['check_in_count'] }}
                                </div>
                            @endif

                            <!-- Check-out indicator -->
                            @if($day['check_out_count'] > 0)
                                <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↑{{ $day['check_out_count'] }}
                                </div>
                            @endif

                            <!-- Enhanced tooltip with booking details -->
                            <div id="tooltip-week2-{{ $loop->index }}" class="mobile-tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                    {{ $day['date']->format('l, F j, Y') }}
                                    @if($day['has_double_booking'])
                                        <span class="text-red-400 ml-2">⚠ DOUBLE BOOKING</span>
                                    @endif
                                </div>

                                @if($day['has_double_booking'])
                                    <div class="mb-3 p-2 bg-red-900 rounded border-l-2 border-red-400">
                                        <div class="font-bold text-red-300">⚠ BOOKING CONFLICTS DETECTED</div>
                                        @foreach($day['double_booking_venues'] as $conflict)
                                            <div class="text-red-200 text-xs mt-1">
                                                {{ $conflict['venue_name'] }}: {{ $conflict['booking_count'] }} overlapping bookings
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($day['check_in_count'] > 0)
                                    <div class="mb-2 p-2 bg-green-800 rounded border-l-2 border-green-400">
                                        <div class="font-medium text-green-300">✓ {{ $day['check_in_count'] }} Check-in{{ $day['check_in_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_ins'] as $checkin)
                                            <div class="text-green-200 text-xs mt-1">{{ $checkin->name }} - {{ $checkin->venue->venue_name }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if($day['check_out_count'] > 0)
                                    <div class="mb-2 p-2 bg-red-800 rounded border-l-2 border-red-400">
                                        <div class="font-medium text-red-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_outs'] as $checkout)
                                            <div class="text-red-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
                                        @endforeach
                                    </div>
                                @endif
                                @foreach($day['bookings'] as $booking)
                                    <div class="mb-2 p-2 bg-gray-800 rounded border-l-2 border-blue-400">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-medium text-white">{{ $booking->name }}</div>
                                                <div class="text-gray-300 text-xs">{{ $booking->venue->venue_name }}</div>
                                            </div>
                                            <div class="font-mono text-xs text-blue-300">{{ $booking->getDisplayBookingId() }}</div>
                                        </div>
                                        <div class="text-gray-400 text-xs mt-1">
                                            {{ \Carbon\Carbon::parse($booking->check_in)->format('M j') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M j') }}
                                            ({{ $booking->nights }} {{ $booking->nights === 1 ? 'night' : 'nights' }})
                                        </div>
                                        <div class="text-green-400 text-xs mt-1 font-medium">
                                            £{{ number_format($booking->total_price, 2) }}
                                        </div>
                                    </div>
                                @endforeach
                                <!-- Arrow pointing down -->
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                    <div class="border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        @if($day['check_out_count'] > 0)
                            <!-- Show blank card with checkout icon when no bookings but checkouts exist -->
                            <div class="group relative bg-gray-100 hover:bg-gray-200 transition-colors duration-200 rounded-lg p-3 h-16 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 mobile-tooltip-trigger"
                                 title="{{ $day['check_out_count'] }} checkout(s)"
                                 data-tooltip-id="checkout-tooltip-week2-{{ $loop->index }}">
                                <div class="text-gray-400 text-xs font-medium">No bookings</div>

                                <!-- Check-out indicator -->
                                <div class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                    ↑{{ $day['check_out_count'] }}
                                </div>

                                <!-- Enhanced tooltip with checkout details (Second week: above card) -->
                                <div id="checkout-tooltip-week2-{{ $loop->index }}" class="mobile-tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                    <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                        {{ $day['date']->format('l, F j, Y') }}
                                        @if($day['has_double_booking'])
                                            <span class="text-red-400 ml-2">⚠ DOUBLE BOOKING</span>
                                        @endif
                                    </div>

                                    @if($day['has_double_booking'])
                                        <div class="mb-3 p-2 bg-red-900 rounded border-l-2 border-red-400">
                                            <div class="font-bold text-red-300">⚠ BOOKING CONFLICTS DETECTED</div>
                                            @foreach($day['double_booking_venues'] as $conflict)
                                                <div class="text-red-200 text-xs mt-1">
                                                    {{ $conflict['venue_name'] }}: {{ $conflict['booking_count'] }} overlapping bookings
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="mb-2 p-2 bg-red-800 rounded border-l-2 border-red-400">
                                        <div class="font-medium text-red-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                        @foreach($day['check_outs'] as $checkout)
                                            <div class="text-red-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
                                        @endforeach
                                    </div>

                                    <!-- Arrow pointing down (connects to card below) -->
                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                        <div class="border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-100 hover:bg-gray-200 transition-colors duration-200 rounded-lg p-3 h-16 flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                                <div class="text-gray-400 text-xs font-medium">No bookings</div>
                            </div>
                        @endif
                    @endif

                    <!-- Today indicator -->
                    @if($day['is_today'])
                        <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Legend -->
    <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600">
        <div class="flex items-center">
            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-1 animate-pulse"></div>
            Today
        </div>

        <div class="flex items-center">
            <div class="w-3 h-3 bg-green-500 rounded-full mr-1"></div>
            Check-ins <span class="ml-1 text-green-600">↓</span>
        </div>
        <div class="flex items-center">
            <div class="w-3 h-3 bg-red-500 rounded-full mr-1"></div>
            Check-outs <span class="ml-1 text-red-600">↑</span>
        </div>
        <div class="flex items-center">
            <div class="w-3 h-3 bg-blue-500 rounded-full mr-1"></div>
            Has Bookings
        </div>
    </div>
</div>
