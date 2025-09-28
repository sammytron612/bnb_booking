<?php

/**
 * Generate secure image URL for property images
 */
function secure_image_url($location, $admin = false) {
    if (empty($location)) {
        return asset('images/placeholder.jpg'); // Default placeholder
    }

    // Handle legacy /storage/ URLs
    if (strpos($location, '/storage/') === 0) {
        // This is a legacy storage URL, return it as-is for now
        return asset(ltrim($location, '/'));
    }

    // Handle new secure filename-only locations
    $filename = basename($location);

    if ($admin) {
        return route('secure.image.admin', ['filename' => $filename]);
    }

    return route('secure.image.public', ['filename' => $filename]);
}

/**
 * Fallback for existing /storage/ URLs to secure URLs
 */
function convert_storage_url($storageUrl) {
    if (strpos($storageUrl, '/storage/property-images/') !== false) {
        $filename = basename($storageUrl);
        return secure_image_url($filename);
    }

    return $storageUrl;
}
