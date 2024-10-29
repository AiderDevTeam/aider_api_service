<?php

namespace App\Http\Services\Api;

use App\Models\Delivery;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class DeliveryService
{
    public function __construct(public Order $order)
    {
    }

    public function deliverWithWegoo(): bool|JsonResponse
    {
        try {
            logger()->info('### DISPATCHING WEGOO DELIVERY SERVICE TO API-GATEWAY ###');
            logger($url = 'api-gateway/api/delivery/create');

            $items = $this->order->carts()->map(function ($cart) {
                $product = $cart->product;
                return [
                    'name' => $product->name,
                    'type' => $product->subCategory->name,
                    'quantity' => $cart->quantity,
                    'price' => ($cart->quantity * $cart->discounted_amount),
                    'weight' => 1
                ];
            })->toArray();

            $delivery = $this->order->delivery;
            $deliveryData = [
                'deliveryExternalId' => $delivery->external_id,
                'currency' => 'GHC',
                'deliveryOption' => $delivery->delivery_option,
                'isPickup' => true,
                'isFulfillmentDelivery' => false,
                'callbackUrl' => env('API_GATEWAY_DELIVERY_CALLBACK'),
                'amountToCollect' => '',
                'serviceType' => Delivery::INTRACITY,
                'isPrepaidDelivery' => true,
                'pickUpAt' => Carbon::now()->toDateTimeString(),
                'recipient' => [
                    'name' => $delivery->recipient->name,
                    'phone' => $delivery->recipient->phone
                ],
                'sender' => [
                    'name' => $delivery->sender->name,
                    'phone' => $delivery->sender->phone
                ],
                'destination' => [
                    'name' => $delivery->destination->destination_name,
                    'city' => $delivery->destination->city,
                    'state' => $delivery->destination->state,
                    'country' => $delivery->destination->country, //Greater Accra Region
                    'countryCode' => $delivery->destination->country_code,
                    'latitude' => $delivery->destination->latitude,
                    'longitude' => $delivery->destination->longitude
                ],
                'origin' => [
                    'name' => $delivery->origin->origin_name,
                    'city' => $delivery->origin->city,
                    'state' => $delivery->origin->state,
                    'country' => $delivery->origin->country,
                    'countryCode' => $delivery->origin->country_code,
                    'latitude' => $delivery->origin->latitude,
                    'longitude' => $delivery->origin->longitude
                ],
                'items' => $items
            ];

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $deliveryData);
            logger('### RESPONSE FROM API-GATEWAY ###');
            logger($response);

            if ($response->successful()) return successfulJsonResponse($response->json());

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public static function getExpressDeliveryFee(array $request)
    {
        try {
            logger('### DISPATCHING GET WEGOO EXPRESS DELIVERY FEE TO API_GATEWAY ###');
            logger($url = 'api-gateway/api/delivery/get-fee');
            logger($request);
            $data = [
                'destinationCountry' => $request['destinationCountry'],
                'destinationName' => $request['destinationName'],
                'destinationState' => $request['destinationState'],
                'originCountry' => $request['originCountry'],
                'originName' => $request['originName'],
                'originState' => $request['originState'],
                'routes' => [
                    'origin' => [
                        'latitude' => $request['originLatitude'],
                        'longitude' => $request['originLongitude']
                    ],
                    'destination' => [
                        'latitude' => $request['destinationLatitude'],
                        'longitude' => $request['destinationLongitude']
                    ]
                ],
                'items' => $request['items']
            ];

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $data);

            logger('### RESPONSE FROM API_GATEWAY ###');
            logger($response);

            if ($response->successful()) return $response->json();
        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
