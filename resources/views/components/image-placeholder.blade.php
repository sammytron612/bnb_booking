@props([
    'badge' => null,
    'badgeColor' => 'blue',
    'price' => null,
    'height' => 'h-80',
    'venue' => null,
    'images' => []
])

@php
    // Use venue property images if venue is provided, otherwise fall back to images prop
    if ($venue && $venue->propertyImages) {
        // Filter for featured images only
        $featuredImage = $venue->propertyImages->where('featured', true)->first();

        if ($featuredImage) {
            $venueImages = [
                [
                    'src' => $featuredImage->location,
                    'alt' => $venue->venue_name . ' - ' . $featuredImage->title
                ]
            ];
        } else {
            // Fallback to first image if no featured images exist
            $firstImage = $venue->propertyImages->first();
            $venueImages = $firstImage ? [
                [
                    'src' => $firstImage->location,
                    'alt' => $venue->venue_name . ' - ' . $firstImage->title
                ]
            ] : [];
        }
    } else {
        // Fallback to default images if no venue or venue images
        $venueImages = !empty($images) ? $images : [
            ['src' => '/storage/lh1.avif', 'alt' => 'Light House - Ocean View'],
        ];
    }

@endphp

<div class="relative {{ $height }}">
    <!-- Main Image Display -->
    <div class="relative w-full h-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
        @if(count($venueImages) > 0)
            @if($venue && $venue->route)
                <a href="{{ route('venue.show', ['route' => $venue->route]) }}" class="block w-full h-full" aria-label="View {{ $venue->venue_name ?? 'venue' }} details and booking">
                    <img
                        src="{{ $venueImages[0]['src'] }}"
                        alt="{{ $venueImages[0]['alt'] }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                        loading="lazy"
                        decoding="async"
                        width="400"
                        height="320"
                    >
                </a>
            @else
                <img
                    src="{{ $venueImages[0]['src'] }}"
                    alt="{{ $venueImages[0]['alt'] }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    decoding="async"
                    width="400"
                    height="320"
                >
            @endif
        @else
            <!-- Fallback placeholder -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm">No Images Available</p>
                </div>
            </div>
        @endif
    </div>

    @if($badge)
        <!-- Property Badge -->
        <div class="absolute top-4 left-4 bg-{{ $badgeColor }}-600 text-white px-3 py-1 rounded-full text-sm font-medium z-10">
            {{ $badge }}
        </div>
    @endif

    @if($price)
        <!-- Price Tag -->
        <div class="absolute top-4 right-4 bg-white dark:bg-gray-800 px-3 py-1 rounded-full text-sm font-bold text-gray-900 dark:text-white z-10">
            {{ $price }}
        </div>
    @endif
</div>

