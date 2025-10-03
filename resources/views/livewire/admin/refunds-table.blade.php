<div>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Refund Management</h3>
                <a href="{{ route('admin.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                </a>
            </div>

            <!-- Search and Filter Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           id="search"
                           wire:model.live="search"
                           placeholder="Search by name, email, booking ID, or venue..."
                           class="w-full p-2 bg-transparent border-0 border-b-2 border-gray-300 focus:border-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Status Filter</label>
                    <select wire:model.live="statusFilter"
                            id="statusFilter"
                            class="w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="partial_refund">Partial Refund</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="fully_refunded">Fully Refunded</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Bookings Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $booking->getDisplayBookingId() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $booking->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $booking->venue->venue_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M j, Y') }} -
                                    {{ \Carbon\Carbon::parse($booking->check_out)->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} nights
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    Total: £{{ number_format($booking->total_price, 2) }}
                                </div>
                                @if($booking->refund_amount > 0)
                                    @php
                                        $remaining = $booking->total_price - $booking->refund_amount;
                                    @endphp
                                    <div class="text-sm text-green-600 font-medium">
                                        £{{ number_format($remaining, 2) }} paid
                                    </div>
                                    <div class="text-sm text-red-600 font-medium">
                                        £{{ number_format($booking->refund_amount, 2) }} refunded
                                    </div>

                                    <!-- ARN Information -->
                                    @if($booking->arns->count() > 0)
                                        <div class="mt-2 space-y-1">
                                            @foreach($booking->arns as $arn)
                                                <div class="text-xs bg-blue-50 text-blue-700 p-2 rounded border">
                                                    <div><strong>Refund:</strong> £{{ number_format($arn->refund_amount, 2) }}</div>
                                                    @if($arn->arn_number)
                                                        <div><strong>ARN:</strong> {{ $arn->arn_number }}</div>
                                                    @else
                                                        <div class="text-yellow-600"><strong>ARN:</strong> Pending...</div>
                                                    @endif
                                                    <div><strong>Status:</strong>
                                                        <span class="@if($arn->status === 'succeeded') text-green-600 @elseif($arn->status === 'failed') text-red-600 @else text-yellow-600 @endif">
                                                            {{ ucfirst($arn->status) }}
                                                        </span>
                                                    </div>
                                                    @if($arn->refund_processed_at)
                                                        <div><strong>Processed:</strong> {{ $arn->refund_processed_at->format('M j, Y g:i A') }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="text-sm text-green-600">
                                        Fully paid
                                    </div>
                                @endif
                            </td>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'partial_refund' => 'bg-yellow-100 text-yellow-800',
                                        'refunded' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @php
                                    $remainingAmount = $booking->total_price - ($booking->refund_amount ?? 0);
                                @endphp
                                @if($remainingAmount > 0)
                                    <button wire:click="openRefundModal({{ $booking->id }})"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                                        </svg>
                                        {{ $booking->refund_amount > 0 ? 'Partial Refund' : 'Refund' }}
                                    </button>
                                @else
                                    <span class="text-sm text-gray-500">Fully Refunded</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No refundable bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- Refund Modal -->
    @if($showRefundModal && $selectedBooking)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Process Refund
                        </h3>
                        <button wire:click="closeRefundModal"
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Booking Details -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-md">
                        <div class="text-sm">
                            <div class="font-medium">Booking: {{ $selectedBooking->booking_id }}</div>
                            <div>Customer: {{ $selectedBooking->name }}</div>
                            <div>Total Paid: £{{ number_format($selectedBooking->total_price, 2) }}</div>
                            @if($selectedBooking->refund_amount > 0)
                                <div class="text-red-600">
                                    Already Refunded: £{{ number_format($selectedBooking->refund_amount, 2) }}
                                </div>
                            @endif
                            @php
                                $remaining = $selectedBooking->total_price - ($selectedBooking->refund_amount ?? 0);
                            @endphp
                            <div class="text-green-600 font-medium">
                                Available for Refund: £{{ number_format($remaining, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Refund Form -->
                    <form wire:submit="processRefund">
                        <div class="mb-4">
                            <label for="refundAmount" class="block text-sm font-medium text-gray-700 mb-1">
                                Refund Amount (£)
                            </label>
                            <input type="number"
                                   id="refundAmount"
                                   wire:model="refundAmount"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $selectedBooking->total_price - ($selectedBooking->refund_amount ?? 0) }}"
                                   class="w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            @error('refundAmount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="refundReason" class="block text-sm font-medium text-gray-700 mb-1">
                                Reason for Refund(Customer will see this message)
                            </label>
                            <textarea id="refundReason"
                                      wire:model="refundReason"
                                      rows="3"
                                      class="p-3 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Enter reason for refund..." required></textarea>
                            @error('refundReason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                    wire:click="closeRefundModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50">
                                <svg wire:loading wire:target="processRefund" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="processRefund">Process Refund</span>
                                <span wire:loading wire:target="processRefund">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
