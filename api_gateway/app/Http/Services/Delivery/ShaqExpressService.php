<?php

namespace App\Http\Services\Delivery;

use App\Models\Delivery;
use App\Models\ShaqExpressDelivery;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class ShaqExpressService
{
    public function __construct(public Delivery $delivery)
    {
    }


    public function logRequest(array $data)
    {
        return ShaqExpressDelivery::create([
            'delivery_id' => $this->delivery->id,
            'request_payload' => json_encode($data)
        ]);
    }

    public function deliver()
    {
        $delivery = $this->delivery;
        try {
            logger('### DISPATCHING CREATE DELIVERY REQUEST TO SHAQ EXPRESS ###');
            logger($url = env('SHAQ_EXPRESS_API_URL') . '/trip/book');

            logger($request = [
                'region' => $delivery->destination->state,
                'pickup_location' => $delivery->origin->origin_name,
                'pickup_lat' => $delivery->origin->latitude,
                'pickup_lng' => $delivery->origin->longitude,
                'pickup_phone_number' => $delivery->sender->phone,
                'start_date' => Carbon::now()->toDateString(),
                'start_time' => Carbon::now()->toTimeString(),
                'package_type' => $delivery->items->first()->type,
                'pickup_note' => '',
                'is_return_trip' => 0,
                'is_scheduled' => 1,
                'vehicle_type_id' => ShaqExpressDelivery::getVehicleType(ShaqExpressDelivery::MOTOR_BIKES),
                'dropoff_location' => $delivery->destination->destination_name,
                'dropoff_lat' => $delivery->destination->latitude,
                'dropoff_lng' => $delivery->destination->longitude,
                'dropoff_phone_number' => $delivery->recipient->phone,
                'dropoff_name' => $delivery->recipient->name,
                'dropoff_client_cash_amount' => 0,
                'customer_notes' => '',
                'webhook_url' => "http://174.138.80.217/api-gateway/webhooks/shaq-express-response-url"
            ]);

            $deliveryLog = $this->logRequest($request);

            $response = Http::withHeaders(jsonHttpHeaders())
                ->withToken(env('SHAQ_EXPRESS_API_KEY'))
                ->post($url, $request);

            logger('### RESPONSE FROM SHAQ EXPRESS AFTER CREATING DELIVERY ###');
            logger($response);

            $deliveryLog->update(['response_payload' => json_encode($response->json())]);

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function getDeliveryFee(array $requestData)
    {
        try {
            logger('## DISPATCHING GET DELIVERY FEE REQUEST TO SHAQ EXPRESS ###');
            logger($url = env('SHAQ_EXPRESS_API_URL') . '/delivery-fee/request');


            logger($request = [
                'region' => $requestData['destinationState'],
                'vehicle_type_id' => ShaqExpressDelivery::getVehicleType($requestData['vehicleType']),
                'pickup_lat' => $requestData['routes']['origin']['latitude'],
                'pickup_lng ' => $requestData['routes']['origin']['longitude'],
                'dropoff_lat' => $requestData['routes']['destination']['latitude'],
                'dropoff_lng' => $requestData['routes']['destination']['longitude']
            ]);

            $response = Http::withHeaders(jsonHttpHeaders())
                ->withToken(env('SHAQ_EXPRESS_API_KEY'))
                ->post($url, $request);

            logger('### RESPONSE FROM SHAQ EXPRESS ###');
            logger($response);

            if ($response->successful()) return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
