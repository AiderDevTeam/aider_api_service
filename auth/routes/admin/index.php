<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VendorServiceController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AdminController::class, 'login']);

Route::get('/roles', [AdminController::class, 'getRoles']);
Route::get('/permissions', [AdminController::class, 'getPermissions']);

Route::middleware('auth:admin')->group(function () {

    Route::post('send-bulk-push-notification', [NotificationController::class, 'sendBulkPushNotification']);

    Route::get('/users', [AdminController::class, 'users']);

    Route::apiResource('/', AdminController::class)->only('store', 'index');

    Route::get('/{admin}', [AdminController::class, 'show']);
    Route::put('/{admin}', [AdminController::class, 'update']);

    Route::post('/logout', [AdminController::class, 'logout']);

    Route::prefix('vendor')->group(function () {
        Route::post('update-product', [VendorServiceController::class, 'updateProduct']);
    });

    Route::middleware('role.admin')->group(function () {
        Route::post('/create-permissions', [AdminController::class, 'createPermission']);
        Route::post('/create-roles', [AdminController::class, 'createRole']);

        Route::post('/assign-roles/{admin}', [AdminController::class, 'assignRole']);
        Route::post('/revoke-roles/{admin}', [AdminController::class, 'revokeRole']);

        Route::post('/assign-permissions/{admin}', [AdminController::class, 'assignPermission']);
        Route::post('/revoke-permissions/{admin}', [AdminController::class, 'revokePermission']);

        Route::put('/update-user/{username}', [AdminController::class, 'updateUser']);
    });

    Route::post('kyc-approval', [AdminController::class, 'verifyUserId']);
});

