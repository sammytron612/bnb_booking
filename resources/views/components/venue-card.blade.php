@props(['venue', 'badgeText', 'badgeColor', 'buttonColor', 'route'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden h-full flex flex-col">
    <!-- Property Image Gallery -->
    <x-image-placeholder
        :title="$venue->venue_name"
        :badge="$badgeText"
        :badge-color="$badgeColor"
        :price="'£' . $venue->price . '/night'"
        :venue="$venue"
    />

    <div class="p-6 md:p-8 flex-grow flex flex-col">
        <div class="flex-grow">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $venue->venue_name }}</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                {{ $venue->description1 }}
            </p>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-2 gap-3 mb-6">
            @foreach($venue->amenities->take(4) as $amenity)
                <div class="flex items-center p-3 rounded-lg">
                    <div class="w-6 h-6 mr-3 text-gray-900 flex items-center justify-center flex-shrink-0">
                        {!! $amenity->svg !!}
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $amenity->title }}
                    </span>
                </div>
            @endforeach
        </div>

        <a href="{{ route('venue.show', $route) }}" type="button" class="self-start inline-block text-center bg-{{ $buttonColor }}-600 hover:bg-{{ $buttonColor }}-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
            View Details & Book
        </a>
    </div>
</div>
