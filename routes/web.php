<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Models\Venue;

Route::get('/', function () {
    $venues = Venue::with('propertyImages')->get();
    return view('home', compact('venues'));
})->name('home');

Route::get('/light-house', function () {
    $venue = Venue::with('propertyImages')->find(1);
    return view('light-house', compact('venue'));
})->name('light-house');

Route::get('/saras', function () {
    $venue = Venue::with('propertyImages')->find(2);
    return view('saras', compact('venue'));
})->name('saras');

// Booking routes
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/venue/{venue}', [BookingController::class, 'getBookingsForVenue'])->name('bookings.venue');
Route::get('/bookings/upcoming', [BookingController::class, 'getUpcomingBookings'])->name('bookings.upcoming');
Route::get('/api/booked-dates', [BookingController::class, 'getBookedDates'])->name('bookings.bookedDates');
Route::patch('/bookings/{booking}/status', [BookingController::class, ':updateStatus'])->name('bookings.updateStatus');

;

// Payment routes
Route::get('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout');
Route::post('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout.post');
Route::get('/payment/success/{booking}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel/{booking}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

// Review routes
Route::get('/reviews/create/{booking}', function (Request $request, $booking) {
    return view('create-review', ['booking' => $booking]);
})->name('reviews.create')->middleware('signed');

// Test route for review link generation
Route::get('/test', [App\Http\Controllers\ReviewLink::class, 'create'])->name('test.review.link');

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
});


require __DIR__.'/auth.php';
