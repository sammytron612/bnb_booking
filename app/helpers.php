<?php

if (!function_exists('csp_nonce')) {
    /**
     * Get the CSP nonce for inline scripts and styles
     * Usage: <script nonce="{{ csp_nonce() }}">
     */
    function csp_nonce(): string
    {
        // Try to get from view shared data
        $factory = app('view');
        $shared = $factory->getShared();
        
        if (isset($shared['cspNonce'])) {
            return $shared['cspNonce'];
        }
        
        // Fallback: try to get from request attributes
        if (request()->attributes->has('csp_nonce')) {
            return request()->attributes->get('csp_nonce');
        }
        
        return '';
    }
}

if (!function_exists('csp_script')) {
    /**
     * Generate a script tag with CSP nonce
     * Usage: {!! csp_script('console.log("Hello");') !!}
     */
    function csp_script(string $code): string
    {
        $nonce = csp_nonce();
        return "<script nonce=\"{$nonce}\">{$code}</script>";
    }
}

if (!function_exists('csp_style')) {
    /**
     * Generate a style tag with CSP nonce
     * Usage: {!! csp_style('body { color: red; }') !!}
     */
    function csp_style(string $css): string
    {
        $nonce = csp_nonce();
        return "<style nonce=\"{$nonce}\">{$css}</style>";
    }
}