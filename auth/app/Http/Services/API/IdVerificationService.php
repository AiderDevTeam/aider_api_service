<?php

namespace App\Http\Services\API;

use App\Http\Requests\VerifyIdRequest;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class IdVerificationService
{
    const VERIFICATION_PROCESSOR = [
        'HUBTEL' => 'hubtel',
        'MARGINS' => 'margins'
    ];

    public static function verify(array $request): PromiseInterface|Response|null
    {
        try {
            logger()->info("### SENDING ID VERIFICATION REQUEST TO API GATEWAY ###");
            logger($url = 'api-gateway/api/id-verification');
            logger($request);
            $response = Http::withHeaders(jsonHttpHeaders())->post($url, $request);

            logger()->info('### API-GATEWAY SERVICE RESPONSE ###');
            logger($response);
            return $response;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function getIdVerificationData(?string $idNumber)
    {
        if (is_null($idNumber))
            return null;

        try {
            logger('## FETCHING USER VERIFICATION DATA FROM API-GATEWAY ###');
            logger($url = `api-gateway/api/get-id-verification-data?idNumber=` . $idNumber);

            sleep(3);
            $response = Http::withHeaders(jsonHttpHeaders())->get($url);
            logger('### RESPONSE FROM API-GATEWAY ###');
            logger($response);

            if ($response->successful() && $data = $response->json()['data']) return $data;

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
