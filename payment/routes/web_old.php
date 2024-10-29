<?php

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
    Route::post('/payment-collection-callback', function (Request $request) {
        $paymentService = new PaymentService();
        $paymentService->checkTransactionStatus($request, Payment::COLLECTION);
    })->name('collection.callback');

    Route::post('/payment-disbursement-callback', function (Request $request) {
        $paymentService = new PaymentService();
        $paymentService->checkTransactionStatus($request, Payment::DISBURSEMENT);
    })->name('disbursement.callback');

    Route::post('/payment-reversal-callback', function (Request $request) {
        $paymentService = new PaymentService();
        $paymentService->checkTransactionStatus($request, Payment::REVERSAL);
    })->name('reversal.callback');

    Route::post('/payment-delivery-callback', function (Request $request) {
        $paymentService = new PaymentService();
        return $paymentService->handleDeliveryPaymentDisbursement($request);
    })->name('delivery.callback');
});


