<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy (CSP)
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy settings. CSP helps prevent XSS attacks
    | by controlling which resources can be loaded on your pages.
    |
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
    ],
    /* */
    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure additional security headers that can be enabled/disabled
    | via environment variables.
    |
    */

    'headers' => [
        'cross_origin_embedder_policy' => env('COEP_ENABLED', false),
        'strict_transport_security' => env('HSTS_ENABLED', false), // Apache handles HSTS
    ],

];
