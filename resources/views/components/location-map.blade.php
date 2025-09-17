@props(['location'])

<div class="mt-8">
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Location Map</h3>
    <div class="rounded-xl overflow-hidden shadow">
        <iframe
            src="https://www.google.com/maps?q={{ urlencode($location) }}&output=embed"
            width="100%"
            height="350"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
