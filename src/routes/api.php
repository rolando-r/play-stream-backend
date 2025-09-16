<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['guest'])->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('auth.register');

    Route::post('/google', [GoogleController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('auth.google.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn(Request $request) => ApiResponse::success('USER_FETCHED', $request->user()))
        ->name('auth.user');

    // Send verification link
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::error('EMAIL_ALREADY_VERIFIED', [], 400);
        }
        $request->user()->sendEmailVerificationNotification();
        return ApiResponse::success('VERIFICATION_LINK_SENT');
    })->middleware('throttle:6,1')
        ->name('auth.email.send');

    // Verify email
    Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return ApiResponse::success('EMAIL_ALREADY_VERIFIED');
        }

        $request->fulfill();
        return ApiResponse::success('EMAIL_VERIFIED');
    })->middleware(['auth:sanctum', 'signed'])
        ->name('auth.email.verify');
});
