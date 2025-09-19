@props([
    'badge' => null,
    'badgeColor' => 'blue',
    'price' => null,
    'height' => 'h-80',
    'images' => [
        ['src' => '/storage/lh1.avif', 'alt' => 'Light House - Ocean View'],
        ['src' => '/storage/lh2.avif', 'alt' => 'Light House - Living Room'],
        ['src' => '/storage/lh3.jpeg', 'alt' => 'Light House - Kitchen'],
        ['src' => '/storage/lh4.avif', 'alt' => 'Light House - Bedroom'],
        ['src' => '/storage/lh5.avif', 'alt' => 'Light House - Bathroom'],
        ['src' => '/storage/lh6.jpeg', 'alt' => 'Light House - Dining Area'],
        ['src' => '/storage/lh7.avif', 'alt' => 'Light House - Balcony'],
        ['src' => '/storage/lh8.avif', 'alt' => 'Light House - Exterior']
    ]
])

@php
    $galleryId = 'gallery-' . uniqid();
@endphp

<div class="relative {{ $height }}" data-gallery="{{ $galleryId }}">
    <!-- Main Image Display -->
    <div class="relative w-full h-full overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-700">
        @if(count($images) > 0)
            <img
                src="{{ $images[0]['src'] }}"
                alt="{{ $images[0]['alt'] }}"
                class="w-full h-full object-cover cursor-pointer"
                data-modal-trigger="{{ $galleryId }}"
                data-image-index="0"
                loading="lazy"
                decoding="async"
                width="400"
                height="320"
            >

            <!-- Thumbnail Grid Overlay -->
            <div class="absolute bottom-4 right-4">
                <button
                    class="bg-black bg-opacity-60 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-opacity-80 transition-all flex items-center"
                    data-modal-trigger="{{ $galleryId }}"
                    data-image-index="0"
                >
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                    {{ count($images) }} Photos
                </button>
            </div>
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

<!-- Modal -->
<div
    id="modal-{{ $galleryId }}"
    class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4"
    data-modal="{{ $galleryId }}"
>
    <div class="relative max-w-6xl max-h-full w-full h-full flex items-center justify-center">
        <!-- Close Button -->
        <button
            class="absolute top-4 right-4 text-white hover:text-gray-300 z-20"
            data-modal-close="{{ $galleryId }}"
        >
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Previous Button -->
        <button
            class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20"
            data-modal-prev="{{ $galleryId }}"
        >
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Next Button -->
        <button
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-20"
            data-modal-next="{{ $galleryId }}"
        >
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
        </button>

        <!-- Main Modal Image -->
        <div class="relative w-full h-full flex items-center justify-center pb-24">
            <img
                id="modal-image-{{ $galleryId }}"
                src="{{ count($images) > 0 ? $images[0]['src'] : '' }}"
                alt="{{ count($images) > 0 ? $images[0]['alt'] : '' }}"
                class="max-w-full max-h-full object-contain" loading="lazy" decoding="async"
            >
        </div>

        <!-- Image Counter -->
        <div class="absolute bottom-20 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 text-white px-3 py-1 rounded-lg text-sm">
            <span id="modal-counter-{{ $galleryId }}">1 / {{ count($images) }}</span>
        </div>

        <!-- Thumbnail Strip -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 bg-black bg-opacity-60 p-2 rounded-lg max-w-full overflow-x-auto">
            @foreach($images as $index => $image)
                <img
                    src="{{ $image['src'] }}"
                    alt="{{ $image['alt'] }}"
                    class="w-16 h-16 object-cover rounded cursor-pointer opacity-60 hover:opacity-100 transition-opacity {{ $index === 0 ? 'ring-2 ring-white opacity-100' : '' }}"
                    data-modal-thumb="{{ $galleryId }}"
                    data-thumb-index="{{ $index }}"
                loading="lazy" decoding="async"
                >
            @endforeach
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <script type="application/json" data-gallery-images="{{ $galleryId }}">
        {!! json_encode($images) !!}
    </script>
</div>
