<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request with CSP headers
     *
     * FEATURE FLAG: Can be disabled via config('security.csp.enabled')
     * EMERGENCY DISABLE: Set CSP_ENABLED=false in .env
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // SAFETY: Check if CSP is enabled via config (can be disabled instantly)
        if (!config('security.csp.enabled', false)) {
            return $response;
        }

        // Simple CSP without nonces - allows unsafe-inline for compatibility
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://checkout.stripe.com https://www.googletagmanager.com https://www.google-analytics.com https://maps.googleapis.com localhost:* ws: wss:",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://maps.googleapis.com localhost:* *.test",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data: localhost:* *.test",
            "img-src 'self' data: https: blob: localhost:* *.test https://maps.googleapis.com https://maps.gstatic.com https://www.google-analytics.com",
            "connect-src 'self' https://api.stripe.com https://checkout.stripe.com https://www.google-analytics.com https://analytics.google.com https://maps.googleapis.com ws: wss: localhost:* *.test",
            "frame-src https://js.stripe.com https://checkout.stripe.com https://hooks.stripe.com https://www.google.com https://maps.google.com https://www.googletagmanager.com",
            "form-action 'self' https://checkout.stripe.com",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
        ];

        // Apply CSP header
        $cspHeader = implode('; ', $csp);

        // SAFETY: Use report-only mode initially if configured
        if (config('security.csp.report_only', false)) {
            $response->headers->set('Content-Security-Policy-Report-Only', $cspHeader);
        } else {
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }

        // Additional security headers (also configurable)
        if (config('security.headers.x_frame_options', true)) {
            $response->headers->set('X-Frame-Options', 'DENY');
        }

        if (config('security.headers.x_content_type_options', true)) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        if (config('security.headers.x_xss_protection', true)) {
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }

        if (config('security.headers.referrer_policy', true)) {
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        }

        // Cross Origin Resource Policy (CORP) - prevents cross-origin resource loading
        if (config('security.headers.cross_origin_resource_policy', true)) {
            $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        }

        // Cross Origin Embedder Policy (COEP) - additional isolation (can break third-party embeds)
        if (config('security.headers.cross_origin_embedder_policy', false)) {
            $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
        }

        // Cross Origin Opener Policy (COOP) - prevents window references
        if (config('security.headers.cross_origin_opener_policy', true)) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        return $response;
    }
}
