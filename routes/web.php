<?php

use App\Http\Controllers\Auth\OtpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'otp.verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'otp.verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/verify-otp', [OtpController::class, 'show'])->name('otp.form');
    Route::get('/resend-otp', [OtpController::class, 'resend'])->name('otp.resend');
    Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
});

require __DIR__ . '/auth.php';
