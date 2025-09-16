<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register'])
    ->middleware(['guest','throttle:10,1'])
    ->name('register');

Route::post('/google', [GoogleController::class, 'login'])
    ->middleware(['guest','throttle:10,1'])
    ->name('google.login');