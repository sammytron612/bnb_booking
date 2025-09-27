<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\IcalController;
use App\Http\Controllers\BookingController;

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
Route::match(['GET', 'OPTIONS'], '/ical/export/{venue_id}', [IcalController::class, 'exportVenueCalendar'])
    ->name('api.ical.export')
    ->where('venue_id', '[0-9]+');

// Test iCal data for import testing
Route::get('/airbnb-test-calendar.ics', [IcalController::class, 'getAirbnbTestIcalData'])->name('api.ical.airbnb.test');
Route::get('/booking-com-test-calendar.ics', [IcalController::class, 'getBookingComTestIcalData'])->name('api.ical.booking-com.test');

// Public booking data API for calendar integration (no caching for real-time updates)
Route::get('/booked-dates', [\App\Http\Controllers\BookingController::class, 'getBookedDates'])->name('api.booked.dates');
