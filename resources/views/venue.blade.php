<x-layouts.app>

    <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-24 py-12">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                {{$venue->venue_name}}
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                {{$venue->description2}}
            </p>
        </div>

        <!-- Image Gallery Layout -->
        <div class="flex flex-col lg:flex-row gap-6 mb-8">
            <div class="w-full lg:w-1/2 h-64 md:h-80 lg:h-96">
                <!-- Featured Image Content -->
            @php
                $featuredImage = $venue->propertyImages->where('featured', true)->first();
                $otherImages = $venue->propertyImages->where('featured', false);
                $allImages = $venue->propertyImages;
            @endphp

            <!-- Main Featured Image (50% width) -->
            <div class="w-full h-full">
                @if($featuredImage)
                <div class="relative w-full h-full rounded-xl overflow-hidden shadow-xl cursor-pointer"
                     data-modal-trigger="{{$venue->venue_name}}-gallery"
                     data-image-index="{{ $allImages->search($featuredImage) }}">
                    <img
                        src="{{ $featuredImage->location }}"
                        alt="{{ $featuredImage->title }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                        loading="lazy"
                    >
                </div>
                @else
                <div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                    <span class="text-gray-500 dark:text-gray-400">No featured image</span>
                </div>
                @endif
            </div>
            </div>

            <!-- Side Images (50% width, split into 4 quadrants) -->
            <div class="w-full lg:w-1/2 h-64 md:h-80 lg:h-96 grid grid-cols-2 gap-4">
                @foreach($otherImages->take(4) as $index => $image)
                    <div class="relative rounded-lg overflow-hidden shadow-lg cursor-pointer h-full"
                         data-modal-trigger="{{$venue->venue_name}}-gallery"
                         data-image-index="{{ $allImages->search($image) }}">
                        <img
                            src="{{ $image->location }}"
                            alt="{{ $image->title }}"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                        @if($loop->last && $otherImages->count() > 4)
                            <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                                <span class="text-white text-lg font-semibold">+{{ $otherImages->count() - 4 }} more</span>
                            </div>
                        @endif
                    </div>
                @endforeach

                @for($i = $otherImages->count(); $i < 4; $i++)
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                @endfor
            </div>
        </div>

        <!-- Property Details -->
        <div class="mt-12 lg:mt-16">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        About {{$venue->venue_name}}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        {{$venue->description3}}
                    </p>

                    <!-- Features Grid -->
                    <!-- Amenities Section -->
                    <x-amenities :venueId="$venue->id" />
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Pricing
                    </h3>
                    <div class="bg-{{ $venue->theme_color }}-50 dark:bg-{{ $venue->theme_color }}-900/20 rounded-lg p-6 mb-6">
                        <div class="text-3xl font-bold text-{{ $venue->theme_color }}-600 dark:text-{{ $venue->theme_color }}-400 mb-2">
                            £{{$venue->price}}<span class="text-lg font-normal text-gray-600 dark:text-gray-300">/night</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Minimum 2-night stay • Includes all amenities
                        </p>
                    </div>

                    <!-- Availability & Booking Button -->
                    <button
                        id="openBookingModal"
                        class="w-full bg-{{ $venue->theme_color }}-600 hover:cursor-pointer hover:bg-{{ $venue->theme_color }}-700 text-white font-bold py-4 px-8 rounded-lg transition-colors text-lg mb-4"
                    >
                        Check Availability & Book Now
                    </button>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Free cancellation up to 48 hours before check-in
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <x-location-map location="{{ $venue->postcode }}"/>

        <!-- Reviews Section -->
        <div class="mt-16">
            <livewire:reviews />
        </div>

    </div>

    <x-booking-modal price="{{ $venue->price }}" venue-id="{{ $venue->id }}" />

    <x-venue-image-modal galleryId="{{$venue->venue_name}}-gallery" :images="$venue->propertyImages" />

    <script src="{{ Vite::asset('resources/js/booking-modal.js') }}" defer></script>
</x-layouts.app>
