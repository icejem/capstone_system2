<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', function () {
        return view('welcome', ['authPanel' => 'login']);
    })
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('login/verify', [AuthenticatedSessionController::class, 'notice'])
        ->name('login.verification.notice');
    Route::get('login/verify/status', [AuthenticatedSessionController::class, 'status'])
        ->name('login.verification.status');
    Route::get('login/verify/complete', [AuthenticatedSessionController::class, 'complete'])
        ->name('login.verification.complete');
    Route::post('login/verify/resend', [AuthenticatedSessionController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('login.verification.resend');
    Route::get('forgot-password', function () {
        return view('welcome', ['authPanel' => 'forgot']);
    })
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::get('login/verify/{verification}/{payload}', [AuthenticatedSessionController::class, 'verify'])
    ->middleware(['web', 'signed', 'throttle:6,1'])
    ->name('login.verification.verify');

Route::get('login/deny/{verification}/{payload}', [AuthenticatedSessionController::class, 'deny'])
    ->middleware(['web', 'signed', 'throttle:6,1'])
    ->name('login.verification.deny');

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('logout', fn () => redirect('/'));

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
