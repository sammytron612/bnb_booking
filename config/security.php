<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP) Configuration
    |--------------------------------------------------------------------------
    |
    | EMERGENCY DISABLE: Set 'enabled' to false to instantly disable CSP
    | SAFE MODE: Set 'report_only' to true to monitor violations without blocking
    |
    */

    'csp' => [
        // MAIN TOGGLE: Set to false to completely disable CSP
        'enabled' => env('CSP_ENABLED', false), // STARTS DISABLED FOR SAFETY

        // SAFE MODE: Reports violations but doesn't block (good for testing)
        'report_only' => env('CSP_REPORT_ONLY', true), // STARTS IN SAFE MODE

        // CSP Reporting endpoint (optional - for monitoring violations)
        'report_uri' => env('CSP_REPORT_URI', null),

        // Custom CSP directives (can be modified as needed)
        'directives' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' https://js.stripe.com https://checkout.stripe.com https://unpkg.com",
            'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com",
            'font-src' => "'self' https://fonts.gstatic.com data:",
            'img-src' => "'self' data: https: blob:",
            'connect-src' => "'self' https://api.stripe.com https://checkout.stripe.com",
            'frame-src' => "https://js.stripe.com https://checkout.stripe.com https://hooks.stripe.com",
            'frame-ancestors' => "'none'",
            'form-action' => "'self' https://checkout.stripe.com",
            'base-uri' => "'self'",
            'object-src' => "'none'",
            'upgrade-insecure-requests' => '',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    |
    | Application-level security headers (Apache handles HSTS)
    | Each header can be individually enabled/disabled
    |
    */

    'headers' => [
        // Standard security headers
        'x_frame_options' => env('SECURITY_X_FRAME_OPTIONS', true),
        'x_content_type_options' => env('SECURITY_X_CONTENT_TYPE_OPTIONS', true),
        'x_xss_protection' => env('SECURITY_X_XSS_PROTECTION', true),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', true),

        // Cross-origin isolation headers
        'cross_origin_resource_policy' => env('SECURITY_CORP', true),
        'cross_origin_embedder_policy' => env('SECURITY_COEP', false), // Disabled - can break integrations
        'cross_origin_opener_policy' => env('SECURITY_COOP', true),

        // Permissions policy (disabled by default)
        'permissions_policy' => env('SECURITY_PERMISSIONS_POLICY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rollback Settings
    |--------------------------------------------------------------------------
    |
    | Emergency settings for quick rollback
    |
    */

    'emergency' => [
        // Set to true to disable ALL security headers (nuclear option)
        'disable_all' => env('SECURITY_EMERGENCY_DISABLE', false),
    ],
];
