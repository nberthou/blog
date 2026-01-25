<?php

use App\Http\Controllers\Auth\EmailVerificationCodeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

require __DIR__.'/settings.php';

Route::middleware(['auth', 'throttle:verification'])->group(function () {
    Route::post('/email/verify-code', [EmailVerificationCodeController::class, 'verify'])
        ->name('verification.verify-code');
    Route::post('/email/resend-code', [EmailVerificationCodeController::class, 'resend'])
        ->name('verification.resend-code');
});
