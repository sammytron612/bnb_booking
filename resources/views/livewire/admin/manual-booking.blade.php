<div>
    <!-- Trigger Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create Manual Booking
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                aria-hidden="true"
                wire:click="closeModal">
            </div>

            <!-- Center container for modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-50"
                 style="position: relative; z-index: 51;">
                <form wire:submit="createBooking">
                    <!-- Modal Header -->
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Create Manual Booking
                            </h3>
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Form Content -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Venue Selection -->
                            <div class="md:col-span-2">
                                <label for="venueId" class="block text-sm font-medium text-gray-700 mb-1">
                                    Venue <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model.live="venueId"
                                    id="venueId"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('venueId') border-red-500 @enderror">
                                    <option value="">Select a venue...</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->id }}">{{ $venue->venue_name }} (£{{ $venue->price }}/night)</option>
                                    @endforeach
                                </select>
                                @error('venueId')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Guest Name -->
                            <div>
                                <label for="guestName" class="block text-sm font-medium text-gray-700 mb-1">
                                    Guest Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="guestName"
                                    id="guestName"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('guestName') border-red-500 @enderror"
                                    placeholder="Enter guest full name">
                                @error('guestName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Guest Email -->
                            <div>
                                <label for="guestEmail" class="block text-sm font-medium text-gray-700 mb-1">
                                    Guest Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    wire:model.live="guestEmail"
                                    id="guestEmail"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('guestEmail') border-red-500 @enderror"
                                    placeholder="guest@example.com">
                                @error('guestEmail')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Guest Phone -->
                            <div>
                                <label for="guestPhone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Guest Phone <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    wire:model.live="guestPhone"
                                    id="guestPhone"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('guestPhone') border-red-500 @enderror"
                                    placeholder="+44 1234 567890">
                                @error('guestPhone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Booking Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model.live="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check-in Date -->
                            <div>
                                <label for="checkIn" class="block text-sm font-medium text-gray-700 mb-1">
                                    Check-in Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    wire:model.live="checkIn"
                                    id="checkIn"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('checkIn') border-red-500 @enderror">
                                @error('checkIn')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check-out Date -->
                            <div>
                                <label for="checkOut" class="block text-sm font-medium text-gray-700 mb-1">
                                    Check-out Date <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    wire:model.live="checkOut"
                                    id="checkOut"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('checkOut') border-red-500 @enderror">
                                @error('checkOut')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Booking Summary -->
                            @if($nights > 0)
                            <div class="md:col-span-2 bg-blue-50 p-4 rounded-md">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Booking Summary</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p><span class="font-medium">Duration:</span> {{ $nights }} night{{ $nights > 1 ? 's' : '' }}</p>
                                    <p><span class="font-medium">Price per night:</span> £{{ number_format($pricePerNight, 2) }}</p>
                                    <p><span class="font-medium">Calculated total:</span> £{{ number_format($totalPrice, 2) }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Total Price (Editable) -->
                            <div>
                                <label for="totalPrice" class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Price (£) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model.live="totalPrice"
                                    id="totalPrice"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('totalPrice') border-red-500 @enderror"
                                    placeholder="0.00">
                                @error('totalPrice')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Admin can override the calculated price if needed</p>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Admin Notes
                                </label>
                                <textarea
                                    wire:model.live="notes"
                                    id="notes"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                                    placeholder="Optional notes about this booking..."></textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">{{ strlen($notes) }}/1000 characters</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Create Booking</span>
                            <span wire:loading>Creating...</span>
                        </button>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('booking_success'))
        <div class="mt-4 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('booking_success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('booking_error'))
        <div class="mt-4 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('booking_error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
