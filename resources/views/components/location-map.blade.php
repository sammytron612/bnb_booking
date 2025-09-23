@props(['location'])

<div class="mt-8">
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Location Map</h3>
    <div class="rounded-xl overflow-hidden shadow">
        <!-- Map Container with Loading State -->
        <div id="map-container" class="relative">
            <!-- Loading Placeholder -->
            <div id="map-loading" class="flex items-center justify-center h-[350px] bg-gray-100 dark:bg-gray-800">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                    <p class="text-gray-600 dark:text-gray-400">Loading map...</p>
                </div>
            </div>

            <!-- Map iframe - hidden initially -->
            <iframe
                id="location-map"
                src="https://www.google.com/maps?q={{ urlencode($location) }}&output=embed&z=15"
                width="100%"
                height="350"
                style="border:0; display:none;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                onload="handleMapLoad()"
                onerror="handleMapError()">
            </iframe>

            <!-- Error State -->
            <div id="map-error" class="hidden items-center justify-center h-[350px] bg-gray-100 dark:bg-gray-800">
                <div class="text-center p-4">
                    <div class="text-4xl mb-2">📍</div>
                    <p class="text-gray-700 dark:text-gray-300 font-medium mb-2">Map temporarily unavailable</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $location }}</p>
                    <button onclick="retryMap()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                        Retry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let mapRetryCount = 0;
const maxRetries = 2;

function handleMapLoad() {
    // Hide loading state and show map
    document.getElementById('map-loading').style.display = 'none';
    document.getElementById('location-map').style.display = 'block';
    console.log('Map loaded successfully');
}

function handleMapError() {
    console.error('Map failed to load');
    showMapError();
}

function showMapError() {
    document.getElementById('map-loading').style.display = 'none';
    document.getElementById('location-map').style.display = 'none';
    document.getElementById('map-error').classList.remove('hidden');
    document.getElementById('map-error').classList.add('flex');
}

function retryMap() {
    if (mapRetryCount < maxRetries) {
        mapRetryCount++;
        console.log(`Retrying map load (attempt ${mapRetryCount})`);

        // Reset states
        document.getElementById('map-error').classList.add('hidden');
        document.getElementById('map-error').classList.remove('flex');
        document.getElementById('map-loading').style.display = 'flex';

        // Reload iframe with cache busting
        const iframe = document.getElementById('location-map');
        const currentSrc = iframe.src;
        iframe.src = '';

        setTimeout(() => {
            iframe.src = currentSrc + '&retry=' + mapRetryCount;
        }, 500);
    } else {
        alert('Map service is currently unavailable. Please try again later.');
    }
}

// Auto-retry after 10 seconds if map fails to load initially
setTimeout(() => {
    const mapLoading = document.getElementById('map-loading');
    if (mapLoading && mapLoading.style.display !== 'none') {
        console.log('Map taking too long to load, showing error state');
        if (mapRetryCount === 0) {
            retryMap();
        } else {
            showMapError();
        }
    }
}, 10000);

// Mobile-specific optimizations
if (window.innerWidth <= 768) {
    // Reduce map height on mobile
    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.getElementById('location-map');
        const loading = document.getElementById('map-loading');
        const error = document.getElementById('map-error');

        if (iframe) iframe.height = '250';
        if (loading) loading.style.height = '250px';
        if (error) error.style.height = '250px';
    });
}
</script>
