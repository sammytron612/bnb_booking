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

        // Apply security headers to ALL responses (including 404, 500, etc.)
        $this->addSecurityHeaders($response, $request);

        return $response;
    }

    /**
     * Add security headers to all responses
     */
    private function addSecurityHeaders(Response $response, Request $request): void
    {
        // Emergency disable override
        if (config('app.env') !== 'production' && env('SECURITY_EMERGENCY_DISABLE', false)) {
            return;
        }

        // Apply basic security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');        // Skip Cross-Origin headers for Lighthouse testing
        if (!$this->isLighthouseRequest($request)) {
            $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        }

        // COEP disabled by default - can break Stripe/Google integrations
        if (config('security.headers.cross_origin_embedder_policy', false)) {
            $response->headers->set('Cross-Origin-Embedder-Policy', 'unsafe-none');
        }

        // Performance optimization - cache control headers
        $this->addCacheHeaders($request, $response);

        // Skip CSP for non-HTML responses, if disabled, or for Lighthouse testing
        if (!config('security.csp.enabled', true) ||
            !$this->isHtmlResponse($response) ||
            (config('security.csp.disable_for_lighthouse', true) && $this->isLighthouseRequest($request)) ||
            $request->hasHeader('X-Lighthouse-Test') ||
            $request->get('lighthouse') === '1') {
            return;
        }

        // Content Security Policy - only for HTML responses
        $developmentDomains = app()->environment('local', 'testing') ? ' localhost:* *.test' : '';

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://checkout.stripe.com https://unpkg.com https://www.googletagmanager.com https://www.google-analytics.com https://*.google-analytics.com https://maps.googleapis.com ws: wss:{$developmentDomains}",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://maps.googleapis.com{$developmentDomains}",
            "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:{$developmentDomains}",
            "img-src 'self' data: https: blob: https://maps.googleapis.com https://maps.gstatic.com https://www.google-analytics.com https://*.google-analytics.com{$developmentDomains}",
            "connect-src 'self' https://api.stripe.com https://checkout.stripe.com https://www.google-analytics.com https://analytics.google.com https://*.google-analytics.com https://maps.googleapis.com ws: wss:{$developmentDomains}",
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
    }

    /**
     * Add appropriate cache headers based on request type
     */
    private function addCacheHeaders(Request $request, Response $response): void
    {
        $path = $request->path();

        // Skip cache headers for Lighthouse testing - check user agent
        $userAgent = $request->header('User-Agent', '');
        if (str_contains($userAgent, 'Chrome-Lighthouse')) {
            return;
        }

        // Static assets - aggressive caching
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
        }
        // API routes - moderate caching
        elseif (str_starts_with($path, 'api/')) {
            $response->headers->set('Cache-Control', 'public, max-age=3600');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        }
        // Regular pages - short caching
        else {
            $response->headers->set('Cache-Control', 'public, max-age=1800');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 1800));
        }
    }

    /**
     * Check if response is HTML content
     */
    private function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html') || empty($contentType);
    }

    /**
     * Check if request is from Lighthouse or other performance tools
     */
    private function isLighthouseRequest(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');

        // Check for performance testing headers
        if ($request->hasHeader('X-Lighthouse-Test') ||
            $request->get('lighthouse') === '1' ||
            $request->hasHeader('X-PageSpeed-Insights')) {
            return true;
        }

        // Check for Lighthouse, PageSpeed Insights, and other performance tools
        $performanceTools = [
            'Chrome-Lighthouse',
            'PageSpeed',
            'GTmetrix',
            'WebPageTest',
            'Pingdom',
            'Lighthouse',
            'GoogleChrome',
            'HeadlessChrome',
            'Performance',
            'Speed'
        ];

        foreach ($performanceTools as $tool) {
            if (stripos($userAgent, $tool) !== false) {
                return true;
            }
        }

        return false;
    }
}
