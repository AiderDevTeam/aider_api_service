<?php

namespace App\Http\Services\Api;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PaymentDisbursementService
{
    public static function process(Delivery $delivery): void
    {
        try {
            logger('### SENDING VENDOR PAYOUT REQUEST TO PAYMENT SERVICE ###');

            $response = Http::withHeaders(jsonHttpHeaders())->post('http://payment/webhooks/payment-delivery-callback', [
                'deliveryExternalId' => $delivery->external_id,
                'deliverySuccessful' => true,
                'vendorUserExternalId' => $delivery->order->vendor->user->external_id,
                'disbursementAmount' => $delivery->order->disbursement_amount,
                'disbursementCallbackUrl' => 'http://vendor/webhooks/disbursement-callback-response'
            ]);

            logger('### RESPONSE FROM PAYMENT SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
