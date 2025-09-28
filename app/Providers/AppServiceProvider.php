<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure rate limiting for API endpoints
     */
    protected function configureRateLimiting(): void
    {
        \Illuminate\Support\Facades\RateLimiter::for('api', function ($request) {
            // Default API rate limiting with user-specific limits
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function ($request, array $headers) {
                    return response()->json([
                        'error' => 'Too many requests. Please try again later.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429, $headers);
                });
        });

        // Strict rate limiting for sensitive endpoints
        \Illuminate\Support\Facades\RateLimiter::for('api-strict', function ($request) {
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perDay(1000)->by($request->ip()),
            ];
        });

        // Lenient rate limiting for public resources
        \Illuminate\Support\Facades\RateLimiter::for('api-public', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->ip());
        });

        // iCal specific rate limiting (for external platform syncing)
        \Illuminate\Support\Facades\RateLimiter::for('ical', function ($request) {
            return [
                \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip()),
                \Illuminate\Cache\RateLimiting\Limit::perHour(500)->by($request->ip()),
            ];
        });
    }
}
