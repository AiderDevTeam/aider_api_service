<?php

namespace App\Http\Services;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PremblyService
{
    public function __construct(private readonly array $requestPayload)
    {
    }

    private function makePostRequest(string $url): PromiseInterface|Response
    {
        $response = Http::withHeaders([
            ...jsonHttpHeaders(),
            'x-api-key' => env('PREMBLY_API_KEY'),
            'app-id' => env('PREMBLY_APP_ID'),
        ])->post($url, $this->requestPayload);

        logger('### RESPONSE FROM PREMBLY SERVICE ###');
        logger($response);

        return $response;
    }

    private function makeFormPostRequest(string $url): PromiseInterface|Response
    {
        $response = Http::asForm()->withHeaders([
            'accept' => 'application/json',
            'X-Api-Key' => env('PREMBLY_API_KEY'),
            'app_id' => env('PREMBLY_APP_ID'),
        ])->post($url, $this->requestPayload);

        logger('### RESPONSE FROM PREMBLY SERVICE ###');
        logger($response);

        return $response;
    }

    public function verifyNIN(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING NIN WITH FACE VERIFICATION REQUEST TO PREMBLY ###');
        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/vnin');
            $response = $this->makeFormPostRequest($url);

            if ($response->successful())
                return $response;
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyNINWithFace(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING NIN WITH FACE VERIFICATION REQUEST TO PREMBLY ###');
        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/nin_w_face');
            $response = $this->makeFormPostRequest($url);

            if ($response->successful())
                return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyBVNWithFace(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING BVN WITH FACE VERIFICATION REQUEST TO PREMBLY ###');
        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/bvn_w_face');

            $response = $this->makeFormPostRequest($url);

            if ($response->successful())
                return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyBVN(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING BVN VERIFICATION REQUEST TO PREMBLY ###');
        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/bvn_validation');
            logger($this->requestPayload);

            $response = $this->makePostRequest($url);

            if ($response->successful())
                return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyDocumentWithFace(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING DOCUMENT WITH FACE VERIFICATION REQUEST TO PREMBLY ###');

        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/document_w_face');

            $response = $this->makeFormPostRequest($url);

            if ($response->successful())
                return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public function verifyDocument(): PromiseInterface|Response|null
    {
        logger('### DISPATCHING DOCUMENT VERIFICATION REQUEST TO PREMBLY ###');

        try {
            logger($url = env('PREMBLY_BASE_URL') . '/identitypass/verification/document');

            $response = $this->makePostRequest($url);

            if ($response->successful())
                return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

}
