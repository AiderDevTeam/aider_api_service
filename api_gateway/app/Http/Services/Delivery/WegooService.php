<?php

namespace App\Http\Services\Delivery;

use App\Models\Delivery;
use App\Models\WegooDelivery;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class WegooService
{
    public function __construct(public Delivery $delivery)
    {
    }

    public function logRequest(array $data): Model
    {
        return WegooDelivery::create([
            'delivery_id' => $this->delivery->id,
            'request_payload' => json_encode($data)
        ]);
    }

    public function deliver()
    {
        $delivery = $this->delivery;
        try {
            logger('### DISPATCHING CREATE DELIVERY REQUEST TO WEGOO ###');
            logger($url = env('WEGOO_API_URL'));

            $deliveryData = [
                'currency' => $delivery->currency,
                'delivery_option' => $delivery->delivery_option,
                'is_pickup' => (boolean)$delivery->is_pickup,
                'is_fulfillment_delivery' => (boolean)$delivery->is_fulfillment_delivery,
                'send_notifications' => false,
                'webhook_url' => 'http://174.138.80.217/api-gateway/webhooks/wegoo-response-url',
                'deliveries' => [
                    [
                        'amount_to_collect' => $delivery->amount_to_collect,
                        'destination' => $delivery->destination->destination_name,
                        'destination_city' => $delivery->destination->city,
                        'destination_state' => $delivery->destination->state,
                        'destination_country' => $delivery->destination->country,
                        'destination_country_code' => $delivery->destination->country_code,
                        'items' => $delivery->getDeliveryItems(),
                        'origin' => $delivery->origin->origin_name,
                        'origin_city' => $delivery->origin->city,
                        'origin_state' => $delivery->origin->state,
                        'origin_country' => $delivery->origin->country,
                        'origin_country_code' => $delivery->origin->country_code,
                        'payload' => [
                            'order_id' => $delivery->external_id
                        ],
                        'is_prepaid_delivery' => (boolean)$delivery->is_prepaid_delivery,
                        'pick_up_at' => Carbon::parse($delivery->pick_up_at)->toIso8601ZuluString(),
                        'recipient' => [
                            'name' => $delivery->recipient->name,
                            'phone' => $delivery->recipient->phone,
                        ],
                        'route' => [
                            'origin' => [
                                'longitude' => $delivery->origin->longitude,
                                'latitude' => $delivery->origin->latitude
                            ],
                            'destination' => [
                                'longitude' => $delivery->destination->longitude,
                                'latitude' => $delivery->destination->latitude
                            ]
                        ],
                        'sender' => [
                            'name' => $delivery->sender->name,
                            'phone' => $delivery->sender->phone
                        ],
                        'service' => $delivery->service_type
                    ]
                ]
            ];

            $requestLog = $this->logRequest($deliveryData);
            logger($deliveryData);

            $response = Http::withHeaders(
                jsonHttpHeaders()
            )->withToken(env('WEGOO_API_KEY'))->post($url, $deliveryData);

            logger('### RESPONSE FROM WEGOO AFTER CREATING DELIVERY ###');
            logger($response);

            $requestLog->update(['response_payload' => json_encode($response->json())]);

            if($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function getDeliveryByTrackingNumber(string $trackingNumber)
    {
        try {
            logger()->info('### DISPATCHING GET DELIVERY DETAILS REQUEST TO WEGOO ###');
            logger($url = env('WEGOO_API_URL') . '/' . $trackingNumber . '/details');

            $response = Http::withHeaders(
                jsonHttpHeaders()
            )->withToken(env('WEGOO_API_KEY'))->get($url);
            logger()->info('### RESPONSE FROM WEGOO ###');
            logger($response);

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public static function getExpressDeliveryFee(array $request)
    {
        try {
            logger('### DISPATCHING GET DELIVERY FEE REQUEST TO WEGOO ###');
            logger($url = env('WEGOO_API_URL') . '/price');

            $deliveryItems = [];
            foreach ($request['items'] as $items) {
                $deliveryItems[] = arrayKeyToSnakeCase($items);
            }

            $response = Http::withHeaders(
                jsonHttpHeaders()
            )->withToken(env('WEGOO_API_KEY'))->post($url, [
                'is_fulfillment_delivery' => false,
                'delivery_option' => 'EXPRESS',
                'details' => [
                    [
                        'destination_country' => $request['destinationCountry'],
                        'destination' => $request['destinationName'],
                        'destination_state' => $request['destinationState'],
                        'origin_country' => $request['originCountry'],
                        'origin' => $request['originName'],
                        'origin_state' => $request['originState'],
                        'routes' => [
                            "origin" => [
                                'latitude' => $request['routes']['origin']['latitude'],
                                'longitude' => $request['routes']['origin']['longitude']
                            ],
                            'destination' => [
                                'latitude' => $request['routes']['destination']['latitude'],
                                'longitude' => $request['routes']['destination']['longitude']
                            ]
                        ],
                        'items' => [
                            $deliveryItems
                        ]
                    ]
                ],
                'type' => 'EXPRESS',
                'service' => 'intracity',
                'vehicle_type' => "motor"
            ]);

            logger()->info('### RESPONSE FROM WEGOO ###');
            logger($response);

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }

        return false;
    }

    public static function deleteDelivery(string $trackingNumber)
    {
        try {
            logger('### DISPATCHING DELETE DELIVERY REQUEST TO WEGOO ###');
            logger($url = env('WEGOO_API_URL') . '/' . $trackingNumber);
            $response = Http::withHeaders(
                jsonHttpHeaders()
            )->withToken(env('WEGOO_API_KEY')
            )->delete($url);

            logger('### RESPONSE FROM WEGOO ###');
            logger($response);

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
