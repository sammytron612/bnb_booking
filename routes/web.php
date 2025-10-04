<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Livewire\Volt\Volt;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OtpController;

use App\Models\Venue;

// Load auth routes first to prevent conflicts
require __DIR__.'/auth.php';

Route::get('/', function () {
    $venues = Venue::with('propertyImages','amenities')->get();
    \Log::info('Homepage loaded with ' . $venues->count() . ' venues');
    return view('home', compact('venues'));
})->name('home');

// Legal pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
})->name('terms-of-service');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
})->name('cookie-policy');

// Dynamic venue route using the route field from the database
Route::get('/venue/{route}', function ($route) {
    $venue = Venue::with('propertyImages','amenities')->where('route', $route)->firstOrFail();

    // Get reviews for SEO - reviews are connected through bookings
    $reviews = \App\Models\Review::whereHas('booking', function($query) use ($venue) {
        $query->where('venue_id', $venue->id);
    })->with(['booking.venue'])->get();

    return view('venue', compact('venue', 'reviews'));
})->name('venue.show');

// OTP verification routes - must be authenticated but not require OTP verification
Route::middleware(['auth'])->prefix('otp')->name('otp.')->group(function () {
    Route::get('/verify', [OtpController::class, 'show'])->name('show');
    Route::post('/verify', [OtpController::class, 'verify'])->name('verify');
    Route::post('/resend', [OtpController::class, 'resend'])->name('resend');
});

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
Route::get('/payment/resume/{booking}', [PaymentController::class, 'resumePayment'])
    ->middleware('signed', 'throttle:5,1')->name('payment.resume');
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

//admin routes with OTP verification
Route::middleware(['auth', 'otp.verification'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::get('/refunds', [AdminController::class, 'refunds'])->name('refunds');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/properties', [AdminController::class, 'properties'])->name('properties');

    // Test/debug routes for admin use only
    Route::get('/test', [App\Http\Controllers\ReviewLink::class, 'create'])->name('test.review.link');
    Route::get('/test-jobs', [App\Http\Controllers\ReviewLink::class, 'testJobs'])->name('test.jobs');
});

// Security: Block common scanning attempts
Route::get('/flux/{any?}', function () {
    abort(404);
})->where('any', '.*')->name('flux.blocked');
