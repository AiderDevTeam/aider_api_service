<?php

namespace App\Http\Services\Api;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryPaymentService
{
    public static function create(Request $request, OrderRequest $orderRequest, Order $order, $vendorId, $totalAmount): void
    {
        logger('### SENDING ORDER COLLECTION REQUEST TO PAYMENT SERVICE ###');
        logger($requestPayload = [
            'deliveryExternalId' => $order->delivery->external_id,
            'description' => "collection for order",
            'callbackUrl' => env('DELIVERY_PAYMENT_CALLBACK'),
            'amount' => $totalAmount + $order->delivery_amount,
            'vendorExternalId' => Vendor::where('id', $vendorId)->first()->user->external_id,
            'collectionWallet' => [
                'externalId' => $orderRequest['walletExternalId'],
                'accountNumber' => $orderRequest['recipientContact'],
                'sortCode' => $orderRequest['recipientSortCode'],
                'accountName' => $orderRequest['recipient']['name']
            ]
        ]);

        $response = Http::withToken($request->bearerToken())->post('payment/api/delivery-payments/create', $requestPayload);

        logger('### PAYMENT SERVICE RESPONSE ###', [$response]);

    }

}
