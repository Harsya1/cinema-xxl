<?php

use App\Livewire\LandingPage;
use App\Livewire\ComingSoon;
use App\Livewire\MovieDetails;
use App\Livewire\UserProfile;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Pos\FnbPos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', LandingPage::class)->name('home');
Route::get('/coming-soon', ComingSoon::class)->name('coming-soon');
Route::get('/movie/{id}', MovieDetails::class)->name('movie.details');

// Guest Auth Routes (Public Users Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // User Profile
    Route::get('/profile', UserProfile::class)->name('profile');
    
    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// POS Routes (Staff Only)
Route::middleware(['auth', 'role:fnb_staff,cashier'])->prefix('pos')->group(function () {
    Route::get('/fnb', FnbPos::class)->name('pos.fnb');
});
