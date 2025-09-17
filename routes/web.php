<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\BookingController;

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
Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
