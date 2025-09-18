<div class="bg-white shadow-xl rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Booking Management</h2>

    @if (session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-2">
            {{ session('success') }}
        </div>
    @endif



    <!-- Search -->
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Search bookings..." class="w-full lg:w-1/3 px-4 py-2 border rounded-lg">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Venue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pay Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 cursor-pointer" wire:key="booking-{{ $booking->id }}" wire:click="openBookingModal({{ $booking->id }})">
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
                            <button type="button" wire:click.stop="openBookingModal({{ $booking->id }})" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                Open
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">No bookings found</td>
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
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Name</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Email</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wide">Phone</p>
                                            <p class="font-semibold text-gray-900">{{ $selectedBooking->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 0v4m-4-4h8m-4 8v4"></path>
                                    </svg>
                                    Booking Details
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div class="text-center bg-white rounded-lg p-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 0v4m-4-4h8m-4 8v4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Check-in</p>
                                        <p class="font-bold text-gray-900">{{ $selectedBooking->depart->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-center bg-white rounded-lg p-4">
                                        <div class="w-12 h-12 bg-red-100 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 0v4m-4-4h8m-4 8v4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Check-out</p>
                                        <p class="font-bold text-gray-900">{{ $selectedBooking->leave->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-center bg-white rounded-lg p-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg mx-auto mb-2 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
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
                            <button type="button" wire:click="closeModal" class="px-6 py-2 rounded-lg border-2 border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                            <button type="button" wire:click="saveBooking" class="px-6 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center">
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
