<?php

namespace App\Http\Services;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Http;

class GuestDeliveryPaymentService
{

    public static function create(User $user, OrderRequest $orderRequest, Order $order, $vendorId, $totalAmount): void
    {
        logger('### SENDING ORDER COLLECTION REQUEST TO PAYMENT SERVICE FOR GUEST ###');
        logger($requestPayload = [
            'deliveryExternalId' => $order->delivery->external_id,
            'description' => "collection for order",
            'callbackUrl' => env('DELIVERY_PAYMENT_CALLBACK'),
            'amount' => $totalAmount + $order->delivery_amount,
            'vendorExternalId' => Vendor::where('id', $vendorId)->first()->user->external_id,
            'guestExternalId' => $user->external_id,
            'collectionWallet' => [
                'externalId' => $orderRequest['walletExternalId'],
                'accountNumber' => $user->phone,
                'sortCode' => $orderRequest['recipientSortCode'],
                'accountName' => $orderRequest['recipient']['name']
            ]
        ]);

        $response = Http::post('payment/api/sys/store-guest-delivery-payment', $requestPayload);

        logger('### PAYMENT SERVICE RESPONSE ###', [$response]);

    }

}
