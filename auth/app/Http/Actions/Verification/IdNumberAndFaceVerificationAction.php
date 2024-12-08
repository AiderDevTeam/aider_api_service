<?php

namespace App\Http\Actions\Verification;

use App\Http\Requests\IdNumberAndFaceVerificationRequest;
use App\Http\Services\PremblyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class IdNumberAndFaceVerificationAction
{
    public function handle(IdNumberAndFaceVerificationRequest $request): JsonResponse
    {
        logger('### ID NUMBER WITH FACE VERIFICATION ACTION ###');
        logger($validatedData = $request->validated());
        try {

            // Removed face identification
            // $response = match ($validatedData['type']) {
            //     'NIN' => $this->verifyNINWithFace($validatedData),
            //     'BVN' => $this->verifyBVNWithFace($validatedData),
            //     default => null
            // };

            $response = match ($validatedData['type']) {
                'NIN' => $this->verifyNIN($validatedData),
                'BVN' => $this->verifyBVN($validatedData),
                default => null
            };

            if (is_null($response) || !$response['verified'])
                return errorJsonResponse(errors: [$response['error'] ?? 'verification failed'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            // Removed face identification
            // if(!($response['verificationData']['face_data']['status'])){
            //     return errorJsonResponse(errors: [$response['verificationData']['face_data']['message'] ?? 'Selfie Verification Failed'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            // }

            return successfulJsonResponse(data: $response['verificationData']);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function verifyBVN(array $data): array
    {
        unset($data['type']);
        $response = (new PremblyService($data))->verifyBVN();

        if (
            is_null($response) || !isset($response->json()['status'])
            || !isset($response->json()['data'])
            || !$response->json()['status']
        ) {
            return ['verified' => false, 'error' => 'bvn verification failed'];
        }
        return ['verified' => true, 'verificationData' => $response->json()['data']];
    }

    private function verifyBVNWithFace(array $data): array
    {
        unset($data['type']);
        $response = (new PremblyService($data))->verifyBVNWithFace();

        if (is_null($response) || !isset($response->json()['status'])
            || !isset($response->json()['data'])
            || !$response->json()['status']) {
            return ['verified' => false, 'error' => 'bvn verification failed'];
        }
        return ['verified' => true, 'verificationData' => $response->json()['data']];
    }

    private function verifyNIN(array $data): array
    {
        unset($data['type']);
        $response = (new PremblyService($data))->verifyNIN();

        if (
            is_null($response) || !isset($response->json()['status']) || !$response->json()['status']
            || !isset($response->json()['nin_data'])
        ) {
            return ['verified' => false, 'error' => 'nin verification failed'];
        }
        return ['verified' => true, 'verificationData' => $response->json()['nin_data']];
    }

    private function verifyNINWithFace(array $data): array
    {
        unset($data['type']);
        $response = (new PremblyService($data))->verifyNINWithFace();

        if (is_null($response) || !isset($response->json()['status']) || !$response->json()['status']
            || !isset($response->json()['nin_data'])) {
            return ['verified' => false, 'error' => 'nin verification failed'];
        }
        return ['verified' => true, 'verificationData' => $response->json()['nin_data']];
    }

}
