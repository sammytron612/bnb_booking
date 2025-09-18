<x-layouts.app>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                The Light House
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Explore every corner of our stunning coastal retreat. From panoramic sea views to elegant interiors,
                discover the luxury and comfort that awaits you at The Light House.
            </p>
        </div>

        <!-- Image Gallery Layout -->
        <div class="flex flex-col lg:flex-row gap-6">
            @php
                $lightHouseImages = [
                    ['src' => '/storage/lh1.avif', 'alt' => 'The Light House - Stunning Ocean View', 'title' => 'Ocean View'],
                    ['src' => '/storage/lh2.avif', 'alt' => 'The Light House - Spacious Living Room', 'title' => 'Living Room'],
                    ['src' => '/storage/lh3.jpeg', 'alt' => 'The Light House - Modern Kitchen', 'title' => 'Modern Kitchen'],
                    ['src' => '/storage/lh4.avif', 'alt' => 'The Light House - Comfortable Bedroom', 'title' => 'Master Bedroom'],
                    ['src' => '/storage/lh5.avif', 'alt' => 'The Light House - Luxury Bathroom', 'title' => 'Luxury Bathroom'],
                    ['src' => '/storage/lh6.jpeg', 'alt' => 'The Light House - Elegant Dining Area', 'title' => 'Dining Area'],
                    ['src' => '/storage/lh7.avif', 'alt' => 'The Light House - Private Balcony', 'title' => 'Private Balcony'],
                    ['src' => '/storage/lh8.avif', 'alt' => 'The Light House - Beautiful Exterior', 'title' => 'Exterior View']
                ];
            @endphp

            <!-- Main Featured Image (50% width) -->
            <div class="w-full lg:w-1/2">
                <div class="relative w-full h-64 md:h-80 lg:h-96 rounded-xl overflow-hidden shadow-xl">
                    <img
                        src="{{ $lightHouseImages[0]['src'] }}"
                        alt="{{ $lightHouseImages[0]['alt'] }}"
                        class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-500"
                        data-modal-trigger="lighthouselh-gallery"
                        data-image-index="0"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-bold mb-1">{{ $lightHouseImages[0]['title'] }}</h3>
                        <p class="text-sm opacity-90">Click to view gallery</p>
                    </div>
                    <div class="absolute top-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-lg text-sm">
                        {{ count($lightHouseImages) }} Photos
                    </div>
                </div>
            </div>

            <!-- Thumbnail Grid (50% width) -->
            <div class="w-full lg:w-1/2">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach(array_slice($lightHouseImages, 1) as $index => $image)
                        <div class="relative cursor-pointer rounded-lg overflow-hidden shadow-md">
                            <img
                                src="{{ $image['src'] }}"
                                alt="{{ $image['alt'] }}"
                                class="w-full h-32 object-cover"
                                data-modal-trigger="lighthouselh-gallery"
                                data-image-index="{{ $index + 1 }}" loading="lazy"
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
                        About The Light House
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        A beacon of luxury overlooking Seaham's iconic lighthouse. This stunning coastal apartment features
                        panoramic sea views, modern amenities, and direct access to the famous seaglass beaches of the Heritage Coast.
                        Perfect for couples seeking a romantic getaway or anyone wanting to experience the magic of Durham's coastline.
                    </p>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            2 Bedrooms
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Panoramic Sea Views
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Free Parking
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                            </svg>
                            Full Kitchen
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Seaglass Beach Access
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                            </svg>
                            High-Speed WiFi
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Pricing
                    </h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                            £120<span class="text-lg font-normal text-gray-600 dark:text-gray-300">/night</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            Minimum 2-night stay • Includes all amenities
                        </p>
                    </div>

                    <!-- Availability & Booking Button -->
                    <button
                        id="openBookingModal"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-colors text-lg mb-4"
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

        <x-location-map location="SR7 7HN"/>

        <!-- Reviews Section -->
        <div class="mt-16">
            <livewire:reviews />
        </div>

    </div>

    <x-booking-modal price="120" venue="The Light House" />

    <!--<x-venue-image-modal />-->

    <script src="{{ Vite::asset('resources/js/booking-modal.js') }}" defer></script>
</x-layouts.app>
