<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;

Route::view('/', 'home')
    ->name('home');
Route::view('/light-house', 'light-house')
    ->name('light-house');

Route::view('/saras', 'saras')
    ->name('saras');

// Booking routes
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/venue/{venue}', [BookingController::class, 'getBookingsForVenue'])->name('bookings.venue');
Route::get('/bookings/upcoming', [BookingController::class, 'getUpcomingBookings'])->name('bookings.upcoming');
Route::get('/api/booked-dates', [BookingController::class, 'getBookedDates'])->name('bookings.bookedDates');
Route::patch('/bookings/{booking}/status', [BookingController::class, ':updateStatus'])->name('bookings.updateStatus');

Route::get('/admin', [AdminController::class,'index'])->name('admin')->middleware('auth');

// Payment routes
Route::get('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout');
Route::post('/payment/checkout/{booking}', [PaymentController::class, 'createCheckoutSession'])->name('payment.checkout.post');
Route::get('/payment/success/{booking}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel/{booking}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');
Route::post('/stripe/webhook', [PaymentController::class, 'webhook'])->name('stripe.webhook');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
