<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Contact Information
    |--------------------------------------------------------------------------
    |
    | These values are used throughout the application for contact information
    | in SEO structured data, contact forms, and business listings.
    |
    */

    'phone' => env('OWNER_PHONE_NO', '+44 191 123 4567'),
    'email' => env('OWNER_EMAIL', env('MAIL_FROM_ADDRESS', 'info@seahamcoastalretreats.com')),

    // Alternative hardcoded values for production if env fails
    'phone_fallback' => '+44 191 123 4567',
    'email_fallback' => 'info@seahamcoastalretreats.com',
];
