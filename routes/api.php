<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\IcalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| API routes run without sessions by default, making them perfect for
| public endpoints like sitemaps, robots.txt, and iCal exports to avoid
| HttpOnly cookie security issues.
|
*/

// Public sitemap routes (no sessions, no cookies)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('api.sitemap.index');
Route::get('/sitemap-main.xml', [SitemapController::class, 'main'])->name('api.sitemap.main');
Route::get('/sitemap-venues.xml', [SitemapController::class, 'venues'])->name('api.sitemap.venues');

// Dynamic robots.txt (no sessions, no cookies)
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nAllow: /\n\n# Disallow admin areas\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /password/\nDisallow: /api/\n\n# Allow important pages\nAllow: /venue/\nAllow: /storage/\n\n# Sitemap location\nSitemap: " . config('app.url') . "/api/sitemap.xml\n\n# Crawl-delay to be respectful\nCrawl-delay: 1";

    return response($content)
        ->header('Content-Type', 'text/plain');
})->name('api.robots');

// iCal API routes (already stateless)
Route::get('/ical/venue/{venueId}/calendars', [IcalController::class, 'getVenueCalendars'])->name('api.ical.venue.calendars');
Route::get('/ical/fetch', [IcalController::class, 'fetchIcalData'])->name('api.ical.fetch');
Route::get('/ical/combined', [IcalController::class, 'getCombinedBookingData'])->name('api.ical.combined');

// iCal export route for external calendar sync (Airbnb, Booking.com, Outlook, etc.)
Route::get('/ical/export/{venue_id}', [IcalController::class, 'exportVenueCalendar'])
    ->name('api.ical.export')
    ->where('venue_id', '[0-9]+');

// Handle OPTIONS requests for CORS (Outlook compatibility)
Route::options('/ical/export/{venue_id}', function () {
    return response('')
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('venue_id', '[0-9]+');

// Public booking data API for calendar integration
Route::get('/booked-dates', function (Request $request) {
    $venueId = $request->get('venue_id');

    if (!$venueId) {
        return response()->json(['error' => 'venue_id parameter required'], 400);
    }

    $bookedDates = \App\Models\Booking::where('venue_id', $venueId)
        ->where('status', '!=', 'cancelled')
        ->select('check_in', 'check_out')
        ->get()
        ->map(function ($booking) {
            return [
                'start' => $booking->check_in->format('Y-m-d'),
                'end' => $booking->check_out->format('Y-m-d'),
            ];
        });

    return response()->json($bookedDates)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('api.booked.dates');
