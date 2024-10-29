<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserCampaignAccessController;
use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

Route::post('send-push-notification/{user}', [NotificationController::class, 'pushNotification']);
Route::post('send-sms-notification', [NotificationController::class, 'sendSmsNotification']);

Route::get('/get-user/{user}', [AuthenticationController::class, 'getUser'])->withoutMiddleware('throttle');
Route::post('/username-check', [AuthenticationController::class, 'checkUsernameExistence']);

Route::post('user/grant-campaign-access/{user}', [UserCampaignAccessController::class, 'store']);

Route::post('user/grant-usertype-to-users', [UserTypeController::class, 'grantUserType']);

Route::get('user-types', [UserTypeController::class, 'index']);

Route::prefix('user-type')->group(function(){
    Route::post('/store', [UserTypeController::class, 'store']);
    Route::put('/update', [UserTypeController::class, 'updateUsersUserType']);
});
