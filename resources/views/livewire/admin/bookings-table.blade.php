<div class="bg-white shadow-xl rounded-lg p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Booking Management</h2>

        @if (session('success'))
            <div class="mb-4 rounded border-green-200 bg-green-50 text-green-800 px-4 py-2">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.index') }}" class="inline-block mb-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>

    <!-- View Toggle -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button
                wire:click="$set('activeView', 'table')"
                wire:loading.attr="disabled"
                wire:target="activeView"
                class="@if($activeView === 'table') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:cursor-pointer"
            >
                <span class="text-lg" wire:loading.remove wire:target="activeView">Table View</span>
                <span wire:loading wire:target="activeView" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </span>
            </button>
            <button
                wire:click="$set('activeView', 'calendar')"
                wire:loading.attr="disabled"
                wire:target="activeView"
                class="@if($activeView === 'calendar') border-blue-500 text-blue-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer hover:cursor-pointer"
            >
                <span class="text-lg" wire:loading.remove wire:target="activeView">Calendar View</span>
                <span wire:loading wire:target="activeView" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </span>
            </button>
        </nav>
    </div>

    @if($activeView === 'calendar')
        <!-- Calendar View -->
        <div wire:key="calendar-view-{{ $activeView }}">
            @livewire('admin.booking-cards')
        </div>
    @else
        <!-- Table View -->
        <div wire:key="table-view-{{ $activeView }}">
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
            <option value="payment_failed">Payment Failed</option>
            <option value="payment_expired">Payment Expired</option>
            <option value="abandoned">Abandoned</option>
            <option value="refunded">Refunded</option>
            <option value="partial_refund">Partial Refund</option>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="text-green-600">£{{ number_format($this->getNetAmount($booking), 2) }}</div>
                            @if($booking->refund_amount > 0)
                                <div class="text-xs text-gray-500">
                                    (£{{ number_format((float)$booking->total_price, 2) }} - £{{ number_format((float)$booking->refund_amount, 2) }})
                                </div>
                                <div class="text-xs font-medium mt-1">
                                    @if($booking->refund_amount >= $booking->total_price)
                                        <span class="text-red-600">Full Refund</span>
                                    @else
                                        <span class="text-orange-600">Partial Refund</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($booking->status === 'refunded') bg-red-100 text-red-800
                                @elseif($booking->status === 'partial_refund') bg-orange-100 text-orange-800
                                @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($booking->status === 'payment_expired') bg-orange-100 text-orange-800
                                @elseif($booking->status === 'abandoned') bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($booking->status === 'refunded')
                                    Fully Refunded
                                @elseif($booking->status === 'partial_refund')
                                    Partial Refund
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                @endif
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
        </div> <!-- End Table View -->
    @endif

    <!-- Edit Booking Modal -->
    @if($showEditModal && $selectedBooking)
        <div wire:key="edit-modal-{{ $selectedBooking->id }}"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-9999"
             wire:click.self="closeEditModal">
            <div class="bg-white rounded-xl shadow-2xl p-0 max-w-4xl w-full mx-4 max-h-[95vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-white">Edit Booking</h3>
                            <p class="text-blue-100 text-sm">{{ $selectedBooking->getDisplayBookingId() }}</p>
                        </div>
                        <button wire:click="closeEditModal" class="text-white hover:text-blue-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(95vh-140px)]">
                    <!-- Guest & Booking Information Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Guest Information Card -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <h4 class="font-bold text-gray-900 text-lg">Guest Information</h4>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-16 text-sm font-medium text-gray-500">Name:</span>
                                    <span class="text-gray-900 font-medium">{{ $selectedBooking->name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-16 text-sm font-medium text-gray-500">Email:</span>
                                    <span class="text-gray-900">{{ $selectedBooking->email }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-16 text-sm font-medium text-gray-500">Phone:</span>
                                    <span class="text-gray-900">{{ $selectedBooking->phone }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details Card -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <h4 class="font-bold text-gray-900 text-lg">Booking Details</h4>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-20 text-sm font-medium text-gray-500">Venue:</span>
                                    <span class="text-gray-900 font-medium">{{ $selectedBooking->venue->venue_name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-20 text-sm font-medium text-gray-500">Check-in:</span>
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($selectedBooking->check_in)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-20 text-sm font-medium text-gray-500">Check-out:</span>
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($selectedBooking->check_out)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-20 text-sm font-medium text-gray-500">Nights:</span>
                                    <span class="text-gray-900 font-medium">{{ $selectedBooking->nights }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-20 text-sm font-medium text-gray-500">Total:</span>
                                    <div>
                                        <span class="text-green-600 font-bold text-lg">£{{ number_format($this->getNetAmount($selectedBooking), 2) }}</span>
                                        @if($selectedBooking->refund_amount > 0)
                                            <div class="text-xs text-gray-500 mt-1">
                                                (£{{ number_format((float)$selectedBooking->total_price, 2) }} - £{{ number_format((float)$selectedBooking->refund_amount, 2) }})
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form Section -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center mb-6">
                            <h4 class="font-bold text-gray-900 text-lg">Edit Booking Information</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Check-in Date Field -->
                            <div>
                                <label for="editCheckIn" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Check-in Date
                                </label>
                                <input wire:model="editCheckIn" type="date" id="editCheckIn" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                @error('editCheckIn') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Check-out Date Field -->
                            <div>
                                <label for="editCheckOut" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Check-out Date
                                </label>
                                <input wire:model="editCheckOut" type="date" id="editCheckOut" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                @error('editCheckOut') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nights Display -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Total Nights
                                </label>
                                <div class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50">
                                    <span id="nightsDisplay" class="text-gray-900 font-bold text-lg">{{ $selectedBooking->nights ?? 0 }} nights</span>
                                    <span class="text-gray-500 text-sm ml-2">(automatically calculated)</span>
                                </div>
                            </div>

                            <!-- Status Field -->
                            <div>
                                <label for="editStatus" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Booking Status
                                </label>
                                <select wire:model="editStatus" id="editStatus" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="payment_failed">Payment Failed</option>
                                    <option value="payment_expired">Payment Expired</option>
                                    <option value="abandoned">Abandoned</option>
                                    <option value="refunded">Refunded</option>
                                    <option value="partial_refund">Partial Refund</option>
                                </select>
                                @error('editStatus') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Payment Status Field -->
                            <div>
                                <label for="editPayment" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Payment Status
                                </label>
                                <select wire:model="editPayment" id="editPayment" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm">
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                                @error('editPayment') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Notes Field (Full Width) -->
                        <div class="mt-6">
                            <label for="editNotes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Internal Notes
                            </label>
                            <textarea wire:model="editNotes" id="editNotes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white shadow-sm resize-none" placeholder="Add any internal notes about this booking... (visible only to admin)"></textarea>
                            @error('editNotes') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
                    <button wire:click="closeEditModal" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-white hover:shadow-md transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button wire:click="saveBooking" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

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

    // Modal cleanup to prevent Livewire component ID conflicts
    window.addEventListener('livewire:init', () => {
        Livewire.on('modal-closed', () => {
            // Clean up any stale component references
            setTimeout(() => {
                // Force garbage collection of unused components
                if (window.Livewire && window.Livewire.all) {
                    const allComponents = window.Livewire.all();
                    allComponents.forEach(component => {
                        if (!document.body.contains(component.el)) {
                            // Component DOM element no longer exists, clean it up
                            component.tearDown?.();
                        }
                    });
                }
            }, 100);
        });
    });

    // Add escape key handler for modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Check if edit modal is open and close it
            const modal = document.querySelector('[wire\\:key^="edit-modal-"]');
            if (modal) {
                // Find the close button and click it
                const closeButton = modal.querySelector('button[wire\\:click="closeEditModal"]');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }
    });

    // Add date change handlers for nights calculation
    function calculateNights() {
        const checkInInput = document.getElementById('editCheckIn');
        const checkOutInput = document.getElementById('editCheckOut');
        const nightsDisplay = document.getElementById('nightsDisplay');

        if (checkInInput && checkOutInput && nightsDisplay) {
            const checkInDate = new Date(checkInInput.value);
            const checkOutDate = new Date(checkOutInput.value);

            if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
                const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                nightsDisplay.textContent = `${nights} night${nights !== 1 ? 's' : ''}`;
            } else {
                nightsDisplay.textContent = '0 nights';
            }
        }
    }

    // Add event listeners when modal opens
    document.addEventListener('change', function(e) {
        if (e.target.id === 'editCheckIn' || e.target.id === 'editCheckOut') {
            calculateNights();
        }
    });

    // Calculate nights when modal first opens
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(calculateNights, 100);
    });

    // Prevent rapid clicking on view toggle buttons
    let viewSwitchInProgress = false;
    document.addEventListener('click', function(e) {
        if (e.target.closest('button[wire\\:click*="activeView"]')) {
            if (viewSwitchInProgress) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            viewSwitchInProgress = true;
            setTimeout(() => {
                viewSwitchInProgress = false;
            }, 1000); // 1 second cooldown
        }
    });
});
</script>
