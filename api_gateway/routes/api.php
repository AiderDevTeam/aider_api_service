<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\IdVerificationController;
use App\Http\Controllers\ProcessorController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/processor', ProcessorController::class)->only(['index', 'store', 'update']);

Route::post('/send-sms', SmsController::class);
Route::post('/send-email', EmailController::class);
Route::post('/push-notifications', [PushNotificationController::class, 'send']);
Route::get('transaction/verify/{reference}', [TransactionController::class, 'verifyTransaction']);
Route::get('list-banks', [ProcessorController::class, 'listBanks']);
Route::get('resolve-account-number', BankAccountController::class);
Route::post('store-transfer-recipient', [TransactionController::class, 'createTransferRecipient']);

Route::prefix('verification')->group(function () {
    Route::post('document', [IdVerificationController::class, 'verifyDocument']);
    Route::post('id-number-with-face', [IdVerificationController::class, 'verifyWithIdNumberAndFace']);
});

Route::prefix('webhooks')->group(function () {
    Route::post('paystack-response', [TransactionController::class, 'paystackWebhookHandler']);
});
