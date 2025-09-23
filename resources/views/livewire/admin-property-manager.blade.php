<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Property Management
            </h1>
            <a href="{{ route('admin.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Dashboard
            </a>
        </div>
        <p class="text-gray-600 dark:text-gray-300">
            Manage venues, images, and pricing
        </p>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Property Selection Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Select Property
                </h2>

                <div class="space-y-3">
                    @foreach($venues as $venue)
                        <button
                            wire:click="selectVenue({{ $venue->id }})"
                            class="hover:cursor-pointer w-full text-left p-4 rounded-lg border transition-colors {{ $selectedVenue && $selectedVenue->id === $venue->id ? 'bg-indigo-50 dark:bg-indigo-900 border-indigo-200 dark:border-indigo-600' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                        >
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">
                                        {{ $venue->venue_name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                        £{{ $venue->price }}/night
                                    </p>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $venue->propertyImages->count() }} images
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-2">
            @if($selectedVenue)
                <!-- Property Details Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Property Details
                    </h2>

                    <form wire:submit.prevent="updateVenue" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Property Name
                                </label>
                                <input
                                    type="text"
                                    wire:model="venueName"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                @error('venueName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Price per Night (£)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    wire:model="venuePrice"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                @error('venuePrice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Address Line 1
                                </label>
                                <input
                                    type="text"
                                    wire:model="venueAddress1"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Street address, building name, etc."
                                >
                                @error('venueAddress1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Address Line 2
                                </label>
                                <input
                                    type="text"
                                    wire:model="venueAddress2"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Apartment, suite, unit, etc. (optional)"
                                >
                                @error('venueAddress2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Postcode
                            </label>
                            <input
                                type="text"
                                wire:model="venuePostcode"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('venuePostcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description 1
                            </label>
                            <textarea
                                wire:model="venueDescription1"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                            @error('venueDescription1') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description 2
                            </label>
                            <textarea
                                wire:model="venueDescription2"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                            @error('venueDescription2') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description 3
                            </label>
                            <textarea
                                wire:model="venueDescription3"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            ></textarea>
                            @error('venueDescription3') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Maximum Guests
                            </label>
                            <input
                                type="number"
                                wire:model="venueGuestCapacity"
                                min="1"
                                max="20"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter maximum number of guests"
                            >
                            @error('venueGuestCapacity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Check-in Instructions
                            </label>
                            <textarea
                                wire:model="venueInstructions"
                                rows="6"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter special instructions for guests (WiFi password, parking info, house rules, etc.)"
                            ></textarea>
                            @error('venueInstructions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-6">
                            <button
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 hover:cursor-pointer text-white font-medium py-2 px-4 rounded-lg transition-colors"
                            >
                                Update Property
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Image Management -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        Image Management
                    </h2>

                    <!-- Upload New Images -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Upload New Images
                        </label>
                        <input
                            type="file"
                            wire:model="newImages"
                            multiple
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                        >
                        @error('newImages.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                        @if($newImages)
                            <button
                                wire:click="uploadImages"
                                class="mt-3 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                            >
                                Upload Images
                            </button>
                        @endif
                    </div>

                    <!-- Existing Images -->
                    @if($existingImages && $existingImages->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($existingImages as $image)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <!-- Image Display -->
                                    <div class="relative group mb-3">
                                        <img
                                            src="{{ $image->location }}"
                                            alt="{{ $image->title }}"
                                            class="w-full h-48 object-cover rounded-lg {{ $image->featured ? 'ring-4 ring-yellow-400' : '' }}"
                                        >

                                        <!-- Image Controls - Always visible, no overlay -->
                                        <div class="absolute inset-0 flex items-center justify-center space-x-3 pointer-events-none">
                                            <button
                                                wire:click="toggleFeatured({{ $image->id }})"
                                                class="pointer-events-auto bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-lg shadow-lg"
                                                title="{{ $image->featured ? 'Remove Featured' : 'Set as Featured' }}"
                                            >
                                                <svg class="w-5 h-5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                </svg>
                                            </button>

                                            <button
                                                wire:click="deleteImage({{ $image->id }})"
                                                class="pointer-events-auto bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-lg"
                                                onclick="return confirm('Are you sure you want to delete this image?')"
                                            >
                                                <svg class="w-5 h-5 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </div>

                                        @if($image->featured)
                                            <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded shadow-lg font-medium">
                                                Featured
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Image Title Editor -->
                                    @if($editingImageId === $image->id)
                                        <div class="space-y-3">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Image Title
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="editingImageTitle"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                                placeholder="Enter image title"
                                            >
                                            @error('editingImageTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            <div class="flex space-x-2">
                                                <button
                                                    wire:click="updateImageTitle"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm"
                                                >
                                                    Save
                                                </button>
                                                <button
                                                    wire:click="cancelEditingImageTitle"
                                                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-sm"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex justify-between items-center">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $image->title }}
                                                </p>
                                            </div>
                                            <button
                                                wire:click="startEditingImageTitle({{ $image->id }}, '{{ addslashes($image->title) }}')"
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                                            >
                                                Edit Title
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            No images uploaded yet
                        </p>
                    @endif
                </div>

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
                <!-- No Property Selected State -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Select a Property
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Choose a property from the sidebar to start managing its details and images.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
