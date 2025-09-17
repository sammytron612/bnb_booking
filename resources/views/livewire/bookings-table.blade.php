<div class="bg-white shadow-xl rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Booking Management</h2>

    @if (session('success'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-2">
            {{ session('success') }}
        </div>
    @endif



    <!-- Search -->
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Search bookings..." class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
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
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->venue }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $booking->depart->format('M d') }} - {{ $booking->leave->format('M d') }}</td>
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
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">No bookings found</td>
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
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center" style="z-index: 99999; position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,.5); animation: fadeIn .2s ease-out;" wire:click.self="closeModal" wire:keydown.escape.window="closeModal" aria-modal="true" role="dialog" tabindex="-1">
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-lg w-full mx-4" style="animation: zoomIn .2s ease-out;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Booking Details</h3>
                        <button type="button" wire:click="closeModal" class="text-2xl leading-none" aria-label="Close">&times;</button>
                    </div>

                    @if($selectedBooking)
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Name</p>
                                    <p class="font-medium">{{ $selectedBooking->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium">{{ $selectedBooking->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium">{{ $selectedBooking->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Venue</p>
                                    <p class="font-medium">{{ $selectedBooking->venue }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Check-in</p>
                                    <p class="font-medium">{{ $selectedBooking->depart->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Check-out</p>
                                    <p class="font-medium">{{ $selectedBooking->leave->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total</p>
                                    <p class="font-medium">£{{ number_format($selectedBooking->total_price, 2) }}</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-200"></div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" wire:model.live="editStatus" class="mt-1 block w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                @error('editStatus')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment" class="block text-sm font-medium text-gray-700">Payment Status</label>
                                <select id="payment" wire:model.live="editPayment" class="mt-1 block w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="0">Unpaid</option>
                                    <option value="1">Paid</option>
                                </select>
                                @error('editPayment')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea id="notes" rows="4" wire:model.live="editNotes" class="p-2 mt-1 block w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Add notes for this booking..."></textarea>
                                @error('editNotes')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border border-gray-300 text-gray-700">Cancel</button>
                        <button type="button" wire:click="saveBooking" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
