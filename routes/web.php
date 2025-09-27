<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SitemapController;
use App\Models\Venue;

// Load auth routes first to prevent conflicts
require __DIR__.'/auth.php';

Route::get('/', function () {
    $venues = Venue::with('propertyImages','amenities')->get();
    \Log::info('Homepage loaded with ' . $venues->count() . ' venues');
    return view('home', compact('venues'));
})->name('home');

// Dynamic venue route using the route field from the database
Route::get('/venue/{route}', function ($route) {
    // SECURITY: Validate route parameter to prevent SQL injection
    if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $route) || strlen($route) > 50) {
        abort(404);
    }

    $venue = Venue::with('propertyImages','amenities')->where('route', $route)->firstOrFail();

    // Get reviews for SEO - reviews are connected through bookings
    $reviews = \App\Models\Review::whereHas('booking', function($query) use ($venue) {
        $query->where('venue_id', $venue->id);
    })->with(['booking.venue'])->get();

    return view('venue', compact('venue', 'reviews'));
})->where('route', '[a-zA-Z0-9\-_]+')->name('venue.show');

// Legal pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
})->name('cookie-policy');

// Booking routes - Protected with authentication for admin access
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings/venue/{venue_id}', [BookingController::class, 'getBookingsForVenue'])->name('bookings.venue');
    Route::get('/bookings/upcoming', [BookingController::class, 'getUpcomingBookings'])->name('bookings.upcoming');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});
// Public API for calendar dates (no sensitive data)
Route::get('/api/booked-dates', [BookingController::class, 'getBookedDates'])->name('bookings.bookedDates');

// iCal API routes
Route::get('/api/ical/venue/{venueId}/calendars', [App\Http\Controllers\IcalController::class, 'getVenueCalendars'])->name('ical.venue.calendars');
Route::get('/api/ical/fetch', [App\Http\Controllers\IcalController::class, 'fetchIcalData'])->name('ical.fetch');
Route::get('/api/ical/combined', [App\Http\Controllers\IcalController::class, 'getCombinedBookingData'])->name('ical.combined');

// iCal export route for external calendar sync (Airbnb, Booking.com, Outlook, etc.)
Route::get('/api/ical/export/{venue_id}', [App\Http\Controllers\IcalController::class, 'exportVenueCalendar'])
    ->name('ical.export')
    ->where('venue_id', '[0-9]+');




// Handle OPTIONS requests for CORS (Outlook compatibility)
Route::options('/api/ical/export/{venue_id}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->header('Access-Control-Max-Age', '86400');
})->where('venue_id', '[0-9]+');

// Sitemap routes
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-main.xml', [App\Http\Controllers\SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-venues.xml', [App\Http\Controllers\SitemapController::class, 'venues'])->name('sitemap.venues');

// Test route for debugging iCal integration
/*Route::get('/test/ical/{venueId?}', function($venueId = 1) {
    $controller = new App\Http\Controllers\IcalController();
    $bookingController = new App\Http\Controllers\BookingController();

    echo "<h1>Calendar Integration Debug - Venue $venueId</h1>";

    echo "<h2>1. Database Bookings (existing API):</h2>";
    $dbRequest = new Illuminate\Http\Request(['venue_id' => $venueId]);
    $dbResponse = json_decode($bookingController->getBookedDates($dbRequest)->getContent(), true);
    echo "<pre>" . json_encode($dbResponse, JSON_PRETTY_PRINT) . "</pre>";

    echo "<h2>2. iCal Calendars Available:</h2>";
    echo "<pre>" . json_encode(json_decode($controller->getVenueCalendars($venueId)->getContent()), JSON_PRETTY_PRINT) . "</pre>";

    echo "<h2>3. iCal Data Only:</h2>";
    $request = new Illuminate\Http\Request(['venue_id' => $venueId]);
    $icalResponse = json_decode($controller->fetchIcalData($request)->getContent(), true);
    echo "<pre>" . json_encode($icalResponse, JSON_PRETTY_PRINT) . "</pre>";

    echo "<h2>4. Combined Data:</h2>";
    $combinedResponse = json_decode($controller->getCombinedBookingData($request)->getContent(), true);
    echo "<pre>" . json_encode($combinedResponse, JSON_PRETTY_PRINT) . "</pre>";

    echo "<h2>5. What Frontend Should Receive:</h2>";
    echo "<strong>Check-in dates (orange):</strong> " . implode(', ', $dbResponse['checkInDates'] ?? []) . "<br>";
    echo "<strong>Check-out dates (green):</strong> " . implode(', ', $dbResponse['checkOutDates'] ?? []) . "<br>";
    echo "<strong>Fully booked from DB (gray):</strong> " . implode(', ', $dbResponse['fullyBookedDates'] ?? []) . "<br>";
    echo "<strong>Additional iCal blocked (gray):</strong> " . implode(', ', array_diff($icalResponse['booked_dates'] ?? [], $dbResponse['bookedDates'] ?? [])) . "<br>";

    echo "<h2>6. All Dates That Should Be Grayed Out:</h2>";
    $allGrayDates = array_unique(array_merge($dbResponse['fullyBookedDates'] ?? [], $icalResponse['booked_dates'] ?? []));
    sort($allGrayDates);
    echo "<strong>Gray dates:</strong> " . implode(', ', $allGrayDates) . "<br>";
});*/

// Payment routes - Checkout protected with signed URLs, success/cancel accessible by Stripe
Route::get('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])
    ->middleware('signed')->name('payment.checkout');
Route::post('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])
    ->middleware('signed')->name('payment.checkout.post');
// Success and cancel routes accessible by Stripe redirects (no signed middleware)
Route::get('/payment/success/{booking}', [PaymentController::class, 'paymentSuccess'])
    ->middleware('throttle:10,1')->name('payment.success');
Route::get('/payment/cancel/{booking}', [PaymentController::class, 'paymentCancel'])
    ->middleware('throttle:10,1')->name('payment.cancel');
// Webhook endpoint - No signed middleware as Stripe needs direct access
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])
    ->middleware('throttle:60,1')->name('stripe.webhook');

// Review routes
Route::get('/reviews/create/{booking}', function (Request $request, $booking) {
    return view('create-review', ['booking' => $booking]);
})->name('reviews.create')->middleware('signed');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

//admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/properties', [AdminController::class, 'properties'])->name('properties');

    // Test/debug routes for admin use only
    Route::get('/test', [App\Http\Controllers\ReviewLink::class, 'create'])->name('test.review.link');
    Route::get('/test-jobs', [App\Http\Controllers\ReviewLink::class, 'testJobs'])->name('test.jobs');
});

// Sitemap routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-main.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-venues.xml', [SitemapController::class, 'venues'])->name('sitemap.venues');

// Dynamic robots.txt
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nAllow: /\n\n# Disallow admin areas\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /password/\nDisallow: /api/\n\n# Allow important pages\nAllow: /venue/\nAllow: /storage/\n\n# Sitemap location\nSitemap: " . config('app.url') . "/sitemap.xml\n\n# Crawl-delay to be respectful\nCrawl-delay: 1";

    return response($content)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.index');
    })->name('dashboard');
});
