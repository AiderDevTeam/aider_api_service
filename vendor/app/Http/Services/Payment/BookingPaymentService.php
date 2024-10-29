<?php

namespace App\Http\Services\Payment;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingPaymentService
{
    public function __construct(public Request $request, public array $payload)
    {

    }

    public function initializeCollection()
    {
        logger('### BOOKING COLLECTION PAYMENT DISPATCHED TO PAYMENT SERVICE ###');

        logger($requestPayload = [
            ...$this->payload,
            'type' => 'collection',
            'paymentType' => 'booking',
        ]);

        try {
            $response = Http::withToken($this->request->bearerToken())->withHeaders(jsonHttpHeaders())
                ->post('http://payment/api/initialize-collection', $requestPayload);

            logger('### RESPONSE FROM PAYMENT SERVICE ###');
            logger($response);

            if ($response->successful())
                return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function initializeDisbursement(array $requestPayload)
    {
        logger('### BOOKING DISBURSEMENT PAYMENT DISPATCHED TO PAYMENT SERVICE ###');
        logger($requestPayload);
        try {
            logger($url = env('PAYMENT_BASE_URL') . '/api/sys/initialize-disbursement');

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $requestPayload);

            logger('### RESPONSE FROM PAYMENT SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
