<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $response = redirect('/');

        // Explicitly clear session cookies to handle strict same-site issues
        return $response->withCookie(cookie()->forget(config('session.cookie')))
                        ->withCookie(cookie()->forget('XSRF-TOKEN'));
    }
}
