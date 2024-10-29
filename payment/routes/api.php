<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

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


Route::middleware('auth.user')->group(function () {
    Route::post('initialize-collection', [TransactionController::class, 'collectionTransaction']);

    Route::apiResource('wallet', WalletController::class);
    Route::post('wallet/set-default/{wallet}', [WalletController::class, 'setDefaultWallet']);

    Route::get('/collection/status-check/{stan}', [TransactionController::class, 'checkTransactionStatus']);
});

Route::get('load-banks', BankController::class);

Route::prefix('sys')->group(function () {
    Route::post('initialize-disbursement', [TransactionController::class, 'disbursementTransaction']);
    Route::get('/collection/status-check/{stan}', [TransactionController::class, 'checkTransactionStatus']);
});

