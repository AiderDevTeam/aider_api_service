<?php

use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TransactionController;
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

Route::prefix('webhooks')->group(function () {
    Route::post('paystack-response', [TransactionController::class, 'paystackBankDisbursementWebhookHandler']);

    Route::post('hp-response-url', [TransactionController::class, 'hubtelPaymentWebhookHandler'])->name('hubtelPayment.callback');
    Route::post('wegoo-response-url', [DeliveryController::class, 'wegooDeliveryWebhookHandler']);
    Route::post('shaq-express-response-url', [DeliveryController::class, 'shaqExpressDeliveryWebhookHandler']);
});
