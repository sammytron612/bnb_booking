<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Models\Venue;

Route::get('/', function () {
    $venues = Venue::with('propertyImages','amenities')->get();
    \Log::info('Homepage loaded with ' . $venues->count() . ' venues');
    return view('home', compact('venues'));
})->name('home');

// Dynamic venue route using the route field from the database
Route::get('/venue/{route}', function ($route) {
    $venue = Venue::with('propertyImages','amenities')->where('route', $route)->firstOrFail();
    return view('venue', compact('venue'));
})->name('venue.show');

// Backward compatibility routes - redirect to dynamic route
/*Route::get('/light-house', function () {
    return redirect()->route('venue.show', ['route' => 'light-house']);
})->name('light-house');

Route::get('/saras', function () {
    return redirect()->route('venue.show', ['route' => 'saras']);
})->name('saras');
*/
// Booking routes
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/venue/{venue_id}', [BookingController::class, 'getBookingsForVenue'])->name('bookings.venue');
Route::get('/bookings/upcoming', [BookingController::class, 'getUpcomingBookings'])->name('bookings.upcoming');
Route::get('/api/booked-dates', [BookingController::class, 'getBookedDates'])->name('bookings.bookedDates');
Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

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

// Test route for email jobs
Route::get('/test-jobs', [App\Http\Controllers\ReviewLink::class, 'testJobs'])->name('test.jobs');

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
