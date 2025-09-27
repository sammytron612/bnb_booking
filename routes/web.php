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
    $venue = Venue::with('propertyImages','amenities')->where('route', $route)->firstOrFail();

    // Get reviews for SEO - reviews are connected through bookings
    $reviews = \App\Models\Review::whereHas('booking', function($query) use ($venue) {
        $query->where('venue_id', $venue->id);
    })->with(['booking.venue'])->get();

    return view('venue', compact('venue', 'reviews'));
})->name('venue.show');

// Backward compatibility routes - redirect to dynamic route
/*Route::get('/light-house', function () {
    return redirect()->route('venue.show', ['route' => 'light-house']);
})->name('light-house');

Route::get('/saras', function () {
    return redirect()->route('venue.show', ['route' => 'saras']);
})->name('saras');
*/
// Booking routes - Protected with authentication for admin access
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings/venue/{venue_id}', [BookingController::class, 'getBookingsForVenue'])->name('bookings.venue');
    Route::get('/bookings/upcoming', [BookingController::class, 'getUpcomingBookings'])->name('bookings.upcoming');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});
// Public API for calendar dates (no sensitive data)
Route::get('/api/booked-dates', [BookingController::class, 'getBookedDates'])->name('bookings.bookedDates');

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
