<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitHeaders
{
    /**
     * Handle an incoming request and add rate limit information headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add headers to API routes
        if (str_starts_with($request->path(), 'api/')) {
            // Add informative headers about API usage
            $response->headers->set('X-API-Version', '1.0');
            $response->headers->set('X-API-Rate-Limit-Info', 'Rate limits vary by endpoint. Check documentation for details.');
            
            // Add security headers for API responses
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            
            // If it's a rate limit error, make it more informative
            if ($response->getStatusCode() === 429) {
                $content = json_decode($response->getContent(), true);
                if (!$content) {
                    $response->setContent(json_encode([
                        'error' => 'Rate limit exceeded',
                        'message' => 'Too many requests. Please try again later.',
                        'retry_after' => $response->headers->get('Retry-After', 60),
                        'documentation' => config('app.url') . '/api/docs'
                    ]));
                    $response->headers->set('Content-Type', 'application/json');
                }
            }
        }

        return $response;
    }
}
