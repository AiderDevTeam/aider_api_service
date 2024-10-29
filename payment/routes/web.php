<?php

use App\Http\Controllers\TransactionController;
use App\Http\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Http\Request;
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
    Route::post('collection-payment-webhook', [TransactionController::class, 'collectionPaymentResponse']);
    Route::post('disbursement-payment-webhook', [TransactionController::class, 'disbursementPaymentResponse']);
});


