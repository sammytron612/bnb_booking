<div class="amenities-section">
    @if($displayAmenities->count() > 0)
        <div class="mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                Amenities
            </h3>

            <!-- Display First 6 Amenities -->

            <div class="grid grid-cols-2 gap-3 mb-4">
                @foreach($displayAmenities as $amenity)
                    <div class="flex items-center p-3  rounded-lg">
                        <div class="w-6 h-6 mr-3 text-gray-900 flex items-center justify-center flex-shrink-0">
                            @if($amenity->svg)
                                {!! $amenity->svg !!}
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $amenity->title }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Show All Amenities Button -->
            @if($hasMore)
                <button
                    onclick="openAmenitiesModal()"
                    class="p-2 bg-blue-700 hover:bg-blue-600 rounded text-white hover:cursor-pointer font-medium text-sm"
                >
                    Show all {{ $amenities->count() }} amenities
                </button>
            @endif
        </div>

        <!-- Amenities Modal -->
        <div id="amenitiesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-600">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                            All Amenities ({{ $amenities->count() }})
                        </h2>
                        <button
                            onclick="closeAmenitiesModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($amenities as $amenity)
                                <div class="flex items-center p-3 rounded-lg">
                                    <div class="w-6 h-6 mr-3 text-gray-900 flex items-center justify-center flex-shrink-0">
                                        @if($amenity->svg)
                                            {!! $amenity->svg !!}
                                        @else
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $amenity->title }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function openAmenitiesModal() {
    document.getElementById('amenitiesModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAmenitiesModal() {
    document.getElementById('amenitiesModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('amenitiesModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAmenitiesModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAmenitiesModal();
    }
});
</script>
