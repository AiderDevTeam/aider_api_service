<?php


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

Route::prefix('webhooks')->group(function () {
    Route::post('delivery-payment-response', [WebhookController::class, 'paymentServiceDeliveryWebhookHandler']); //for collection
    Route::post('api-gateway-delivery-response', [WebhookController::class, 'apiGatewayDeliveryWebhookHandler']);
    Route::post('disbursement-callback-response', [WebhookController::class, 'disbursementCallbackWebhookHandler']); //for disbursement
    Route::post('api-gateway-incentive-disbursement-response', [WebhookController::class, 'incentiveDisbursementWebhookHandler']); //for incentive (cash) disbursement
});
