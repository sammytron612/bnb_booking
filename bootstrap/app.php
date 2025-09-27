<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
        ]);

        // SAFE CSP IMPLEMENTATION: Only applies if enabled in config
        // Can be disabled instantly via CSP_ENABLED=false in .env
        $middleware->web(append: [
            \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);

        // Create alias for easier reference
        $middleware->alias([
            'csp' => \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
