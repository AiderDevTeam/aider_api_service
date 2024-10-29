<?php


use App\Http\Controllers\AdminMetricController;
use App\Http\Controllers\CustomJobController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VASController;
use App\Http\Controllers\WalletController;
use App\Http\Resources\WalletResource;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionReceiptService;
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


Route::middleware('auth.user')->group(function() {
    Route::apiResource('/user', UserController::class)->only('index', 'store');

    Route::prefix('/wallets')->group(function () {
        Route::get('', [WalletController::class, 'index']);
        Route::post('/create', [WalletController::class, 'store']);
        Route::get('/details', [WalletController::class, 'show']);
        Route::put('/update', [WalletController::class, 'update']);
        Route::post('/set-default', [WalletController::class, 'setDefaultWallet']);
    });

    Route::prefix('/vas-payments')->group(function () {
        Route::get('/', [VASController::class, 'index']);
        Route::post('/create', [VASController::class, 'store']);
    });

    Route::prefix('/delivery-payments')->group(function () {
        Route::get('/', [DeliveryController::class, 'index']);
        Route::post('/create', [DeliveryController::class, 'store']);
    });

});

Route::post('vas-discount', [VASController::class, 'discount']);

Route::get('/transaction-receipt-download/{external_id}', function ($external_id) {
    return ($external_id && ($transaction = Transaction::findWithExternalId($external_id)))
        ? (new TransactionReceiptService())->createTransactionReceipt($transaction) : '';
})->name('download-invoice');

Route::put('update-transaction/{transaction}', [TransactionController::class, 'updateTransaction']);
Route::put('update-payment/{payment}', [TransactionController::class, 'updatePayment']);
Route::get('transaction/{transaction}', [TransactionController::class, 'index']);

Route::post('run-custom-job', CustomJobController::class);

Route::prefix('/sys')->group(function () {
    Route::apiResource('admin-metrics', AdminMetricController::class);
    Route::post('/store-guest-delivery-payment', [DeliveryController::class, 'storeGuest']);

    Route::get('/get-referral-payout-wallet/{userExternalId}', function ($userExternalId) {
        return ($userExternalId && (
            $wallet = User::getByExternalId($userExternalId)?->getReferralPayoutWallet())
        )
            ? successfulJsonResponse(new WalletResource($wallet)) : errorJsonResponse(errors:['No referral payout wallet found for user'], message: "No wallet found", statusCode: 404);
    })->name('get-referral-payout-wallet');

    Route::get('/get-payout-wallet/{userExternalId}', function ($userExternalId) {
        return ($userExternalId && (
            $wallet = User::getByExternalId($userExternalId)?->getPayoutWallet())
        )
            ? successfulJsonResponse(new WalletResource($wallet)) : errorJsonResponse(errors:['No payout wallet found for user']);
    })->name('get-payout-wallet');
});

//Route::prefix('/sys')->group(function () {
//    Route::post('sync-firestore-data', SyncFirestoreModelController::class);
//});
