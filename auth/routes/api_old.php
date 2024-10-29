<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CustomJobController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SyncModelController;
use App\Http\Controllers\UserCampaignAccessController;
use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/', [AuthenticationController::class, 'index']);
        Route::put('/', [AuthenticationController::class, 'update']);
        Route::post('logout', [AuthenticationController::class, 'logout']);

        Route::post('update-usertype', [UserTypeController::class, 'updateUsersUserType']);

        Route::post('verify-id', [AuthenticationController::class, 'verifyId']);

        Route::post('add-address', [AuthenticationController::class, 'addAddress']);
        Route::put('update-address/{address}', [AuthenticationController::class, 'updateAddress']);

        Route::get('campaign-access/{user}', [UserCampaignAccessController::class, 'getCampaignAccesses']);

        Route::post('deactivate-account', [AuthenticationController::class, 'accountDeactivation']);
    });

    Route::post('login', [AuthenticationController::class, 'login'])->name('login');

    Route::post('/signup', [AuthenticationController::class, 'signUp']);

    Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);
});

Route::apiResource('/usertype', UserTypeController::class)->only('index', 'store');

Route::post('suggest-username', [AuthenticationController::class, 'suggestUsername']);

Route::post('check-phone', [AuthenticationController::class, 'checkPhone']);

Route::post('manually-sync', [SyncModelController::class, 'manuallySync']);

Route::post('run-custom-job', CustomJobController::class);
