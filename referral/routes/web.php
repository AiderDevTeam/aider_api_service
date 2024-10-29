<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'webhooks'], function () {
    Route::post('reward/{stan}', [TransactionController::class, 'updateTransaction']);
    Route::post('api-gateway-referral-reward-disbursement-response', [WebhookController::class, 'cashRewardDisbursementWebhook']);
});
