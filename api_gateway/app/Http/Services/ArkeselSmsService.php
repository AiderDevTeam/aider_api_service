<?php

namespace App\Http\Services;

use App\Jobs\SmsJob;
use Exception;
use Illuminate\Support\Facades\Http;

class ArkeselSmsService extends SmsJob
{
    protected function sendSms(): void
    {
        try {
            $requestPayload = [
                'to' => $this->phone,
                'from' => $this->from ?? 'POYNT', //env('COMPANY_NAME'),
                'sms' => $this->message,
                'action' => 'send-sms'
            ];
            logger()->info('### DISPATCHING SMS REQUEST TO ARKESEL ###');
            logger($requestPayload);

            $response = Http::get(
                env('ARKESEL_SMS_BASE_URL'),
                [
                    'api_key' => env('ARKESEL_SMS_API_KEY'),
                    ...$requestPayload
                ]
            );

            logger()->info($response->json());

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
