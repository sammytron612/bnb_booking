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

    <!-- 7-Day Calendar -->
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Next 7 Days Overview
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($calendarData as $day)
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
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition-all duration-200 rounded-lg p-3 cursor-pointer group shadow-sm hover:shadow-md transform hover:scale-105"
                                 title="{{ $day['booking_count'] }} booking(s)">
                                <div class="text-white text-xs font-bold">
                                    {{ $day['booking_count'] }}
                                </div>
                                <div class="text-blue-100 text-xs">
                                    {{ $day['booking_count'] === 1 ? 'booking' : 'bookings' }}
                                </div>

                                <!-- Enhanced tooltip with booking details -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 pointer-events-none shadow-xl">
                                    <div class="font-bold mb-2 text-blue-300 border-b border-gray-700 pb-1">
                                        {{ $day['date']->format('l, F j, Y') }}
                                    </div>
                                    @foreach($day['bookings'] as $booking)
                                        <div class="border-b border-gray-700 pb-2 mb-2 last:border-b-0 last:pb-0 last:mb-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="font-semibold text-white">{{ $booking->name }}</div>
                                                <div class="font-mono text-xs text-blue-300">{{ $booking->getDisplayBookingId() }}</div>
                                            </div>
                                            <div class="text-gray-300 flex items-center mt-1">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $booking->venue }}
                                            </div>
                                            <div class="text-gray-400 text-xs mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $booking->check_in->format('M j') }} - {{ $booking->check_out->format('M j') }}
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
                            <div class="bg-gray-100 hover:bg-gray-200 transition-colors duration-200 rounded-lg p-3 h-16 flex flex-col items-center justify-center border-2 border-dashed border-gray-300">
                                <div class="text-gray-400 text-xs font-medium">No bookings</div>
                            </div>
                        @endif

                        <!-- Today indicator -->
                        @if($day['is_today'])
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                        @endif

                        <!-- Weekend indicator -->
                        @if($day['is_weekend'])
                            <div class="absolute top-0 left-0 w-2 h-2 bg-yellow-400 rounded-full"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Legend -->
        <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded-full mr-1 animate-pulse"></div>
                Today
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-yellow-400 rounded-full mr-1"></div>
                Weekend
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
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Booked</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 cursor-pointer" wire:key="booking-{{ $booking->id }}" wire:click="openBookingModal({{ $booking->id }})">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-gray-600">{{ $booking->getDisplayBookingId() }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->venue }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">£{{ number_format($booking->total_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->status === 'confirmed')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Confirmed
                                </span>
                            @elseif($booking->status === 'cancelled')
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Cancelled
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->is_paid)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                    Paid
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                    Unpaid
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->pay_method)
                                <span class="text-sm text-gray-900 capitalize">{{ $booking->pay_method }}</span>
                            @else
                                <span class="text-sm text-gray-400">On Arrival</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $booking->created_at->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button type="button" wire:click.stop="openBookingModal({{ $booking->id }})" class="px-3 py-1 text-sm bg-blue-600 hover:pointer-cursor text-white rounded hover:bg-blue-700">
                                Open
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-500">No bookings found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $bookings->links() }}</div>

    <!-- Modal (teleported to body to avoid stacking/overflow issues) -->
    <div wire:teleport="body">
        @if($showBooking)
            <div class="fixed inset-0 bg-black/60 overflow-y-auto backdrop-blur-sm" style="z-index: 99999; position: fixed; inset: 0; background: rgba(0,0,0,.6); animation: fadeIn .3s ease-out;" wire:click.self="closeModal" wire:keydown.escape.window="closeModal" aria-modal="true" role="dialog" tabindex="-1">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto my-8 overflow-hidden" style="animation: slideUp .3s ease-out;">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-white">Booking Details</h3>
                                </div>
                                <button type="button" wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-colors" aria-label="Close">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    @if($selectedBooking)
                        <div class="p-6">
                            <!-- Guest Information -->
                            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Guest Information
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Name</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Venue</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->venue }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="bg-blue-50 rounded-xl p-4 mb-6">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    Booking Details
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="text-center bg-white rounded-lg p-4">

                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Check-in</p>
                                        <p class="font-bold text-gray-900">{{ $selectedBooking->check_in->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-center bg-white rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Check-out</p>
                                        <p class="font-bold text-gray-900">{{ $selectedBooking->check_out->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-center bg-white rounded-lg p-4">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Price</p>
                                        <p class="font-bold text-2xl text-green-600">£{{ number_format($selectedBooking->total_price, 2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Management Section -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Manage Booking
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Status
                                        </label>
                                        <select id="status" wire:model.live="editStatus" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                                            <option value="pending">🟡 Pending</option>
                                            <option value="confirmed">🟢 Confirmed</option>
                                            <option value="cancelled">🔴 Cancelled</option>
                                        </select>
                                        @error('editStatus')
                                            <p class="text-sm text-red-600 mt-1 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="payment" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 104 0c0-.85-.1-1.7-.3-2.5M15 9c0-.85-.1-1.7-.3-2.5"></path>
                                            </svg>
                                            Payment Status
                                        </label>
                                        <select id="payment" wire:model.live="editPayment" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                                            <option value="0">💳 Unpaid</option>
                                            <option value="1">✅ Paid</option>
                                        </select>
                                        @error('editPayment')
                                            <p class="text-sm text-red-600 mt-1 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 text-gray-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Notes
                                    </label>
                                    <textarea id="notes" rows="4" wire:model.live="editNotes" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors resize-none" placeholder="Add internal notes for this booking..."></textarea>
                                    @error('editNotes')
                                        <p class="text-sm text-red-600 mt-1 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="bg-gray-100 px-6 py-4 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Changes are saved automatically
                        </div>
                        <div class="flex gap-3">
                            <button type="button" wire:click="closeModal" class="px-6 py-2 rounded-lg border-2 border-gray-300 hover:bg-gray-400 hover:pointer-cursor text-gray-700 font-medium hover:bg-gray-50 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="button" wire:click="saveBooking" class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:pointer-cursor  font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
