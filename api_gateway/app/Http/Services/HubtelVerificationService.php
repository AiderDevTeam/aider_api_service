<?php

namespace App\Http\Services;

use App\Http\Requests\IdVerificationRequest;
use App\Models\IdVerification;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class HubtelVerificationService
{
    public function __construct(public IdVerificationRequest $request)
    {
    }

    public function verifyId(): JsonResponse
    {
        try {
            $hubtelResponse = $this->sendVerificationRequestToHubtel();

            if ($hubtelResponse->successful() && $response = $hubtelResponse->json()) {
                if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0000' && isset($response['Data'])) {

                    $fullName = explode(' ', $response['Data']['Name']);

                    if (IdVerification::query()->updateOrCreate(
                        ['id_number' => $this->request->validated('idNumber')],
                        [
                            'forenames' => $fullName[0] ?? '',
                            'surname' => end($fullName) ?? '',
                            'birth_date' => $response['Data']['DateOfBirth'] ?? '',
                            'gender' => $response['Data']['Gender'] ?? '',
                            'type' => 'ghana-card'
                        ]
                    ))
                        return successfulJsonResponse(message: 'Id verification started. pending approval');
                }
                return errorJsonResponse(message: 'Id verification failed. Check id number and try again.');
            }
            return errorJsonResponse(message: 'Id verification failed');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    public function sendVerificationRequestToHubtel(): PromiseInterface|Response
    {
        $data = $this->request->validated();
        logger('### DISPATCHING ID VERIFICATION REQUEST TO HUBTEL ###');
        logger($url = env('HUBTEL_ID_VERIFICATION_BASE_URL') . '/' .
            env('HUBTEL_COLLECTION_MERCHANT_ID') .
            '/idcard/verify?idtype=ghanacard&idnumber=' .
            $data['idNumber']);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(env('HUBTEL_API_ID') . ':' . env('HUBTEL_API_KEY')),
            'Cache-Control' => 'no-cache'])
            ->get($url);

        logger('### HUBTEL VERIFICATION RESPONSE ###');
        logger($response->json());

        return $response;
    }
}
