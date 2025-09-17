<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::view('/', 'home')
    ->name('home');
Route::view('/saras', 'light-house')
    ->name('light-house');

Route::view('/saras', 'saras')
    ->name('saras');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php';
