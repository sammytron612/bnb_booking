<div>
    @if(session('amenity_message'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('amenity_message') }}
        </div>
    @endif

    @if($venueId)
        <!-- Amenities Management -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                Amenities Management
            </h2>

            <!-- Add New Amenity -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Add New Amenity
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Amenity Title
                        </label>
                        <input
                            type="text"
                            wire:model="newAmenityTitle"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="e.g., Free WiFi"
                        >
                        @error('newAmenityTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            SVG Icon Code (Optional)
                        </label>
                        <textarea
                            wire:model="newAmenitySvg"
                            rows="2"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="<svg class='w-5 h-5' ...></svg>"
                        ></textarea>
                        @error('newAmenitySvg') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <button
                    wire:click="addAmenity"
                    class="mt-3 bg-green-600 hover:cursor-pointer hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                    Add Amenity
                </button>
            </div>

            <!-- Existing Amenities -->
            @if($venueAmenities && $venueAmenities->count() > 0)
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Current Amenities ({{ $venueAmenities->count() }})
                    </h3>
                    @foreach($venueAmenities as $amenity)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                            @if($editingAmenityId === $amenity->id)
                                <!-- Edit Mode -->
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Amenity Title
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="editingAmenityTitle"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                            @error('editingAmenityTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                SVG Icon Code
                                            </label>
                                            <textarea
                                                wire:model="editingAmenitySvg"
                                                rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                            ></textarea>
                                            @error('editingAmenitySvg') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            wire:click="updateAmenity"
                                            class="bg-green-600 hover:bg-green-700 hover:cursor-pointer text-white px-3 py-1 rounded text-sm"
                                        >
                                            Save
                                        </button>
                                        <button
                                            wire:click="cancelEditingAmenity"
                                            class="bg-gray-500 hover:cursor-pointer hover:bg-gray-600 text-white px-3 py-1 rounded text-sm"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- View Mode -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 mr-3 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                            @if($amenity->svg)
                                                {!! $amenity->svg !!}
                                            @else
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">
                                                {{ $amenity->title }}
                                            </p>
                                            @if($amenity->svg)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Has custom icon
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            wire:click="startEditingAmenity({{ $amenity->id }}, {{ json_encode($amenity->title) }}, {{ json_encode($amenity->svg) }})"
                                            class="hover:cursor-pointer text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            wire:click="deleteAmenity({{ $amenity->id }})"
                                            class="hover:cursor-pointer text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm"
                                            onclick="return confirm('Are you sure you want to delete this amenity?')"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                    No amenities added yet
                </p>
            @endif
        </div>
    @else
        <!-- No Venue Selected State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    Select a Property
                </h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Choose a property to manage its amenities.
                </p>
            </div>
        </div>
    @endif
</div>
