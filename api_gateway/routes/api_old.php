<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\DataBundlePackageController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryProcessorController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\IdVerificationController;
use App\Http\Controllers\ProcessorController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/processor', ProcessorController::class)->only(['index', 'store', 'update']);
Route::get('/data-bundles', [DataBundlePackageController::class, 'dataBundlePackages'])->name('dataBundles.route');

Route::post('sms', [SmsController::class, 'send']);

Route::post('file-upload', FileUploadController::class);

Route::get('/accounts/resolve', BankAccountController::class);

Route::post('/firestore', FirestoreController::class)->withoutMiddleware(['throttle']);

Route::post('id-verification', IdVerificationController::class);

Route::prefix('delivery')->group(function () {
    Route::post('create', [DeliveryController::class, 'createDelivery']);
    Route::get('get-by-tracking-number/{trackingNumber}', [DeliveryController::class, 'getDeliveryByTrackingNumber']);
    Route::post('get-fee', [DeliveryController::class, 'getDeliveryFee']);
    Route::post('delete', [DeliveryController::class, 'deleteDelivery']);
    Route::apiResource('processor', DeliveryProcessorController::class)->only('index', 'store');
    Route::put('processor/{deliveryProcessor}', [DeliveryProcessorController::class, 'update']);
});

Route::get('get-disbursement-transaction/{transactionExternalId}', [TransactionController::class, 'getTransaction'])->withoutMiddleware('throttle');
Route::get('get-collection-transaction/{transactionExternalId}', [TransactionController::class, 'getCollectionTransaction'])->withoutMiddleware('throttle');

Route::get('get-id-verification-data', [IdVerificationController::class, 'getVerificationData']);
