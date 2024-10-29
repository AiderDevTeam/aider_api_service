<?php

use App\Http\Controllers\ActionPoyntController;
use App\Http\Controllers\SyncModelController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
Route::post('/update-poynt-balance/{userExternalId}', [UserController::class, 'updateUserPoynt']);
Route::get('/poynt-balance/{userExternalId}', [UserController::class, 'getpoyntBalance']);
});

Route::apiResource('admin/action-poynt', ActionPoyntController::class)->only(['index', 'store']);

Route::post('manually-sync', [SyncModelController::class, 'sync']);
