<x-layouts.app>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Sara's
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Explore every corner of our stunning coastal retreat. With a modern and elegant interior,
                discover the luxury and comfort that awaits you at Sara's.
            </p>
        </div>

        <!-- Image Gallery Layout -->
        <div class="flex flex-col lg:flex-row gap-6">
            @php
                $featuredImage = $venue->propertyImages->where('featured', true)->first();
                $otherImages = $venue->propertyImages->where('featured', false);
                $allImages = $venue->propertyImages;
            @endphp

            <!-- Main Featured Image (50% width) -->
            <div class="w-full lg:w-1/2">
                @if($featuredImage)
                <div class="relative w-full h-64 md:h-80 lg:h-96 rounded-xl overflow-hidden shadow-xl cursor-pointer"
                     data-modal-trigger="{{$venue->venue_name}}-gallery"
                     data-image-index="{{ $allImages->search($featuredImage) }}">
                    <img
                        src="{{ $featuredImage->location }}"
                        alt="{{ $featuredImage->desc }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                    <div class="absolute bottom-4 left-4 text-white pointer-events-none">
                        <h3 class="text-xl font-bold mb-1">{{ $featuredImage->desc }}</h3>
                        <p class="text-sm opacity-90">Click to view gallery</p>
                    </div>
                    <div class="absolute top-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-lg text-sm pointer-events-none">
                        {{ count($allImages) }} Photos
                    </div>
                </div>
                @endif
            </div>

            <!-- Thumbnail Grid (50% width) -->
            <div class="w-full lg:w-1/2">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($otherImages as $image)
                        <div class="relative cursor-pointer rounded-lg overflow-hidden shadow-md">
                            <img
                                src="{{ $image->location }}"
                                alt="{{ $image->desc }}"
                                class="w-full h-32 object-cover"
                                data-modal-trigger="{{$venue->venue_name}}-gallery"
                                data-image-index="{{ $allImages->search($image) }}"
                                loading="lazy"
                            >
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Property Information Section -->
        <div class="mt-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        About Sara's
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        A beacon of luxury overlooking in the heart of Seaham. This stunning coastal apartment features
                        modern amenities, free breakfast and a short walk to the famous seaglass beaches of the Heritage Coast.
                        Perfect for couples seeking a romantic getaway or anyone wanting to experience the magic of Durham's coastline.
                    </p>

                    <!-- Amenities Section -->
                    <x-amenities :venueId="$venue->id" />
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Pricing
                    </h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                            £{{ $venue->price }}<span class="text-lg font-normal text-gray-600 dark:text-gray-300">/night</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            Minimum 2-night stay • Includes all amenities
                        </p>
                    </div>

                    <!-- Availability & Booking Button -->
                    <button
                        id="openBookingModal"
                        class="w-full bg-blue-600 hover:cursor-pointer hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-colors text-lg mb-4"
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
