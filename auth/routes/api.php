<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserIdentificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    Route::middleware('auth')->group(function () {
        Route::get('/', [AuthenticationController::class, 'index']);
        Route::post('logout', [AuthenticationController::class, 'logout']);
        Route::put('/', [AuthenticationController::class, 'update']);

        Route::post('deactivate-account', [AuthenticationController::class, 'accountDeactivation']);

        Route::post('/upload-profile-photo', [AuthenticationController::class, 'uploadProfilePhoto']);

    });

    Route::post('login', [AuthenticationController::class, 'login'])->name('login');
    Route::post('/signup', [AuthenticationController::class, 'signUp']);
    Route::post('signup-otp', OTPController::class);

    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);
    Route::post('verify-email', [AuthenticationController::class, 'verifyEmail']);
    Route::post('verify-display-name', [AuthenticationController::class, 'verifyDisplayName']);

    Route::prefix('identification')->group(function () {
        Route::post('/initialize', [UserIdentificationController::class, 'initialize']);
    });

});

