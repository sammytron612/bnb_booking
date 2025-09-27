<div class="bg-white shadow-xl rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Booking Management</h2>

    @if (session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.index') }}" class="inline-block mb-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
        Back to Dashboard
    </a>

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
                       <div class="relative hover:z-[70] bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                                 title="{{ $day['booking_count'] }} booking(s)"
                                 data-tooltip-id="tooltip-week1-{{ $loop->index }}">
                                <div class="text-white text-xs font-bold">
                                    {{ $day['booking_count'] }}
                                </div>
                                <div class="text-blue-100 text-xs">
                                    {{ $day['booking_count'] === 1 ? 'booking' : 'bookings' }}
                                </div>

                                <!-- Check-in indicator -->
                                @if($day['check_in_count'] > 0)
                                    <div class="absolute -top-2 -right-2 bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↓{{ $day['check_in_count'] }}
                                    </div>
                                @endif

                                <!-- Check-out indicator -->
                                @if($day['check_out_count'] > 0)
                                    <div class="absolute -top-2 -left-2 bg-amber-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↑{{ $day['check_out_count'] }}
                                    </div>
                                @endif

                                <!-- Enhanced tooltip with booking details (First week: below card, above second week) -->
                                <div id="tooltip-week1-{{ $loop->index }}" class="mobile-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                    <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                        {{ $day['date']->format('l, F j, Y') }}
                                    </div>

                                    @if($day['check_in_count'] > 0)
                                        <div class="mb-2 p-2 bg-green-800 rounded border-l-2 border-green-400">
                                            <div class="font-medium text-green-300">✓ {{ $day['check_in_count'] }} Check-in{{ $day['check_in_count'] > 1 ? 's' : '' }}</div>
                                            @foreach($day['check_ins'] as $checkin)
                                                <div class="text-green-200 text-xs mt-1">{{ $checkin->name }} - {{ $checkin->venue->venue_name }}</div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($day['check_out_count'] > 0)
                                        <div class="mb-2 p-2 bg-amber-800 rounded border-l-2 border-amber-400">
                                            <div class="font-medium text-amber-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                            @foreach($day['check_outs'] as $checkout)
                                                <div class="text-amber-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
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
                                <!-- Show checkout card when no bookings but checkouts exist -->
                                <div class="relative hover:z-[70] bg-gradient-to-br from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                                     title="{{ $day['check_out_count'] }} checkout(s)"
                                     data-tooltip-id="checkout-tooltip-week1-{{ $loop->index }}">
                                    <div class="text-white text-xs font-bold">
                                        {{ $day['check_out_count'] }}
                                    </div>
                                    <div class="text-amber-100 text-xs">
                                        {{ $day['check_out_count'] === 1 ? 'checkout' : 'checkouts' }}
                                    </div>

                                    <!-- Check-out indicator -->
                                    <div class="absolute -top-2 -left-2 bg-amber-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↑{{ $day['check_out_count'] }}
                                    </div>

                                    <!-- Enhanced tooltip with checkout details (First week: below card) -->
                                    <div id="checkout-tooltip-week1-{{ $loop->index }}" class="mobile-tooltip absolute top-full left-1/2 transform -translate-x-1/2 mt-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                        <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                            {{ $day['date']->format('l, F j, Y') }}
                                        </div>

                                        <div class="mb-2 p-2 bg-amber-800 rounded border-l-2 border-amber-400">
                                            <div class="font-medium text-amber-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                            @foreach($day['check_outs'] as $checkout)
                                                <div class="text-amber-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
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
                       <div class="relative hover:z-[70] bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                                 title="{{ $day['booking_count'] }} booking(s)"
                                 data-tooltip-id="tooltip-week2-{{ $loop->index }}">
                                <div class="text-white text-xs font-bold">
                                    {{ $day['booking_count'] }}
                                </div>
                                <div class="text-blue-100 text-xs">
                                    {{ $day['booking_count'] === 1 ? 'booking' : 'bookings' }}
                                </div>

                                <!-- Check-in indicator -->
                                @if($day['check_in_count'] > 0)
                                    <div class="absolute -top-2 -right-2 bg-green-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↓{{ $day['check_in_count'] }}
                                    </div>
                                @endif

                                <!-- Check-out indicator -->
                                @if($day['check_out_count'] > 0)
                                    <div class="absolute -top-2 -left-2 bg-amber-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↑{{ $day['check_out_count'] }}
                                    </div>
                                @endif

                                <!-- Enhanced tooltip with booking details -->
                                <div id="tooltip-week2-{{ $loop->index }}" class="mobile-tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                    <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                        {{ $day['date']->format('l, F j, Y') }}
                                    </div>

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
                                <!-- Show checkout card when no bookings but checkouts exist -->
                                <div class="relative hover:z-[70] bg-gradient-to-br from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105 mobile-tooltip-trigger"
                                     title="{{ $day['check_out_count'] }} checkout(s)"
                                     data-tooltip-id="checkout-tooltip-week2-{{ $loop->index }}">
                                    <div class="text-white text-xs font-bold">
                                        {{ $day['check_out_count'] }}
                                    </div>
                                    <div class="text-amber-100 text-xs">
                                        {{ $day['check_out_count'] === 1 ? 'checkout' : 'checkouts' }}
                                    </div>

                                    <!-- Check-out indicator -->
                                    <div class="absolute -top-2 -left-2 bg-amber-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-md">
                                        ↑{{ $day['check_out_count'] }}
                                    </div>

                                    <!-- Enhanced tooltip with checkout details (Second week: above card) -->
                                    <div id="checkout-tooltip-week2-{{ $loop->index }}" class="mobile-tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-[120] pointer-events-none shadow-xl">
                                        <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                            {{ $day['date']->format('l, F j, Y') }}
                                        </div>

                                    @if($day['check_out_count'] > 0)
                                        <div class="mb-2 p-2 bg-amber-800 rounded border-l-2 border-amber-400">
                                            <div class="font-medium text-amber-300">✗ {{ $day['check_out_count'] }} Check-out{{ $day['check_out_count'] > 1 ? 's' : '' }}</div>
                                            @foreach($day['check_outs'] as $checkout)
                                                <div class="text-amber-200 text-xs mt-1">{{ $checkout->name }} - {{ $checkout->venue->venue_name }}</div>
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
                                    <!-- Arrow pointing down (connects to card below) -->
                                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2">
                                        <div class="border-l-4 border-r-4 border-b-4 border-l-transparent border-r-transparent border-b-gray-900"></div>
                                    </div>
                                </div>
                            </div>                                        <!-- Arrow pointing down (connects to card below) -->
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

    <!-- Search -->
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Search bookings..." class="w-full lg:w-1/3 px-4 py-2 border rounded-lg">

        <!-- Search Legend -->
        <div class="mt-2 text-sm text-gray-600">
            <div class="flex items-center space-x-1 mb-1">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Searchable fields:</span>
            </div>
            <div class="pl-5 flex flex-wrap gap-x-4 gap-y-1">
                <span class="inline-flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                    Guest Name
                </span>
                <span class="inline-flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                    Email Address
                </span>
                <span class="inline-flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                    Venue
                </span>
                <span class="inline-flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                    Booking ID
                </span>
            </div>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="mb-6">
        <select wire:model.live="statusFilter" class="px-4 py-2 border rounded-lg">
            <option value="">All Statuses</option>
            <option value="confirmed">Confirmed</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Bookings Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('booking_id')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Booking ID</span>
                            @if($sortBy === 'booking_id')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('name')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Guest</span>
                            @if($sortBy === 'name')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('venue')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Venue</span>
                            @if($sortBy === 'venue')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('created_at')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Date Booked</span>
                            @if($sortBy === 'created_at')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('check_in')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Check In</span>
                            @if($sortBy === 'check_in')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('check_out')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Check Out</span>
                            @if($sortBy === 'check_out')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('nights')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Nights</span>
                            @if($sortBy === 'nights')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('total_price')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Total Price</span>
                            @if($sortBy === 'total_price')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button wire:click="sortByField('status')" class="flex items-center space-x-1 hover:bg-gray-100 hover:cursor-pointer p-2 rounded transition-colors duration-150">
                            <span>Status</span>
                            @if($sortBy === 'status')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <span class="font-mono text-sm text-gray-600">{{ $booking->getDisplayBookingId() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->venue->venue_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->nights }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">£{{ number_format($booking->total_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button wire:click="editBooking({{ $booking->id }})" class="text-white  bg-blue-600 hover:bg-blue-700 hover:cursor-pointer rounded-lg p-2 mr-3">Edit</button>
                            <button wire:click="deleteBooking({{ $booking->id }})" class="text-white bg-red-600 hover:bg-red-700 hover:cursor-pointer rounded-lg p-2" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">No bookings found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $bookings->links() }}
    </div>

    <!-- Edit Booking Modal -->
    @if($showEditModal && $selectedBooking)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-9999">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Edit Booking - {{ $selectedBooking->getDisplayBookingId() }}</h3>
                    <button wire:click="closeEditModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Guest Information</h4>
                        <p><strong>Name:</strong> {{ $selectedBooking->name }}</p>
                        <p><strong>Email:</strong> {{ $selectedBooking->email }}</p>
                        <p><strong>Phone:</strong> {{ $selectedBooking->phone }}</p>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Booking Details</h4>
                        <p><strong>Venue:</strong> {{ $selectedBooking->venue->venue_name }}</p>
                        <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($selectedBooking->check_in)->format('d/m/Y') }}</p>
                        <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($selectedBooking->check_out)->format('d/m/Y') }}</p>
                        <p><strong>Nights:</strong> {{ $selectedBooking->nights }}</p>
                        <p><strong>Total Price:</strong> £{{ number_format($selectedBooking->total_price, 2) }}</p>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="font-semibold text-gray-900 mb-4">Edit Booking Information</h4>

                    <div class="space-y-4">
                        <!-- Status Field -->
                        <div>
                            <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="editStatus" id="editStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('editStatus') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Payment Status Field -->
                        <div>
                            <label for="editPayment" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                            <select wire:model="editPayment" id="editPayment" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="0">Unpaid</option>
                                <option value="1">Paid</option>
                            </select>
                            @error('editPayment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Notes Field -->
                        <div>
                            <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea wire:model="editNotes" id="editNotes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about this booking..."></textarea>
                            @error('editNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="closeEditModal" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button wire:click="saveBooking" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Save Changes</button>
                </div>
            </div>
        </div>
    @endif

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile tooltip functionality
    let activeTooltip = null;

    // Check if device is mobile/touch-enabled
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    console.log('Touch device detected:', isTouchDevice); // Debug log

    function initializeMobileTooltips() {
        // Add touch event listeners to all tooltip triggers
        const tooltipTriggers = document.querySelectorAll('.mobile-tooltip-trigger');

        console.log('Found tooltip triggers:', tooltipTriggers.length); // Debug log

        tooltipTriggers.forEach((trigger, index) => {
            const tooltipId = trigger.getAttribute('data-tooltip-id');
            const tooltip = document.getElementById(tooltipId);

            console.log('Processing trigger', index, 'with tooltip ID:', tooltipId); // Debug log

            if (tooltip) {
                // Remove any existing event listeners to prevent duplicates
                trigger.removeEventListener('touchend', trigger._touchHandler);

                // Create touch handler
                trigger._touchHandler = function(e) {
                    e.preventDefault(); // Prevent double-tap zoom and other default behaviors
                    e.stopPropagation(); // Stop event bubbling

                    console.log('Touch detected on trigger:', tooltipId); // Debug log

                    // Hide any currently active tooltip
                    if (activeTooltip && activeTooltip !== tooltip) {
                        activeTooltip.classList.remove('opacity-100');
                        activeTooltip.classList.add('opacity-0');
                    }

                    // Toggle this tooltip
                    if (activeTooltip === tooltip) {
                        // Hide if already showing
                        tooltip.classList.remove('opacity-100');
                        tooltip.classList.add('opacity-0');
                        activeTooltip = null;
                    } else {
                        // Show this tooltip
                        tooltip.classList.remove('opacity-0');
                        tooltip.classList.add('opacity-100');
                        activeTooltip = tooltip;
                    }
                };

                // Use touchend instead of touchstart for better mobile UX
                trigger.addEventListener('touchend', trigger._touchHandler, { passive: false });

                // Also handle regular clicks for desktop
                trigger.addEventListener('click', function(e) {
                    if (!isTouchDevice) {
                        e.preventDefault();
                        trigger._touchHandler(e);
                    }
                });
            } else {
                console.warn('Tooltip not found for ID:', tooltipId); // Debug warning
            }
        });

        // Add tap outside to close functionality
        document.addEventListener('touchend', function(e) {
            if (activeTooltip) {
                let clickedInsideTooltip = false;

                // Check if clicked inside any trigger or tooltip
                const allTriggers = document.querySelectorAll('.mobile-tooltip-trigger');
                const allTooltips = document.querySelectorAll('.mobile-tooltip');

                allTriggers.forEach(trigger => {
                    if (trigger.contains(e.target)) {
                        clickedInsideTooltip = true;
                    }
                });

                allTooltips.forEach(tooltip => {
                    if (tooltip.contains(e.target)) {
                        clickedInsideTooltip = true;
                    }
                });

                // Hide tooltip if clicked outside
                if (!clickedInsideTooltip) {
                    activeTooltip.classList.remove('opacity-100');
                    activeTooltip.classList.add('opacity-0');
                    activeTooltip = null;
                }
            }
        });
    }

    // Initialize on load
    if (isTouchDevice) {
        initializeMobileTooltips();
    }

    // Re-initialize when Livewire updates the component
    document.addEventListener('livewire:navigated', initializeMobileTooltips);
    document.addEventListener('livewire:load', initializeMobileTooltips);

    // Also reinitialize after any DOM changes (for Livewire updates)
    const observer = new MutationObserver(function(mutations) {
        let shouldReinitialize = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.classList?.contains('mobile-tooltip-trigger') || node.querySelector?.('.mobile-tooltip-trigger'))) {
                        shouldReinitialize = true;
                    }
                });
            }
        });

        if (shouldReinitialize && isTouchDevice) {
            setTimeout(initializeMobileTooltips, 100);
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
    </script>
</div>
