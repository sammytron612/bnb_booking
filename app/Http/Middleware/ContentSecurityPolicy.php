<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Handle an incoming request with security headers
     *
     * Apache handles HSTS - this middleware focuses on CSP and other application headers
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip if CSP is disabled
        if (!config('security.csp.enabled', true)) {
            return $response;
        }

        // Content Security Policy - allows necessary external resources
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
        if (config('security.csp.report_only', false)) {
            $response->headers->set('Content-Security-Policy-Report-Only', $cspHeader);
        } else {
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }

        // Security headers (Apache handles HSTS)
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Cross-origin isolation headers
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // COEP disabled by default - can break Stripe/Google integrations
        if (config('security.headers.cross_origin_embedder_policy', false)) {
            $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
        }

        return $response;
    }
}
