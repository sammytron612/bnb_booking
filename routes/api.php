<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

// iCal API routes - Specialized rate limiting for external platforms
Route::middleware(['throttle:ical'])->group(function () {
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
});

// Public booking data API - Strict rate limiting (high-value data, prevent abuse)
Route::middleware(['throttle:api-strict'])->group(function () {
    Route::get('/booked-dates', [BookingController::class, 'getBookedDates'])->name('api.booked.dates');
});
