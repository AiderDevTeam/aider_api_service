<?php

namespace App\Http\Services\API;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PremblyKYCService
{
    public function __construct(private readonly array $requestPayload)
    {
    }

    private function postRequest(string $url): PromiseInterface|Response
    {
        $response = Http::withHeaders(jsonHttpHeaders())->post($url, $this->requestPayload);
        logger('### RESPONSE FROM API-GATEWAY SERVICE ###');
        logger($response);
        return $response;
    }

    public function idNumberWithSelfieVerification(): PromiseInterface|Response|null
    {
        try {
            logger('### DISPATCHING ' . $this->requestPayload['type'] . ' VERIFICATION TO API-GATEWAY ###');
            logger($url = env('API_GATEWAY_BASE_URL') . '/api/verification/id-number-with-face');

            return $this->postRequest($url);

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyDocument(): PromiseInterface|Response|null
    {
        try {
            logger('### DISPATCHING DOCUMENT VERIFICATION TO API-GATEWAY ###');
            logger($url = env('API_GATEWAY_BASE_URL') . '/api/verification/document');

            return $this->postRequest($url);

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
