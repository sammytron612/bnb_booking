<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply OTP verification to authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        // Skip OTP verification for OTP-related routes to prevent infinite redirects
        if ($request->routeIs('otp.*')) {
            return $next($request);
        }

        // Check if user has verified OTP in this session
        if (!session('otp_verified')) {
            // Redirect to OTP verification page
            return redirect()->route('otp.show')->with('message', 'Please verify your identity with the OTP code sent to your email.');
        }

        return $next($request);
    }
}
