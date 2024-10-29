<?php

namespace App\Http\Actions\Verification;

use App\Http\Requests\DocumentVerificationRequest;
use App\Http\Services\PremblyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DocumentVerificationAction
{
    public function handle(DocumentVerificationRequest $request): JsonResponse
    {
        logger('### DOCUMENT VERIFICATION ACTION ###');
        logger($request->except('docImage', 'selfieImage'));
        try {

            // $response = (new PremblyService(arrayKeyToSnakeCase($request->validated())))->verifyDocumentWithFace();
            $response = (new PremblyService(arrayKeyToSnakeCase($request->validated())))->verifyDocument();

            if (is_null($response) || !isset($response->json()['status'])
                || !isset($response->json()['data']) || !$response->json()['status']) {
                return errorJsonResponse(errors:["Document Verification Failed"], message: 'verification failed', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // Removed face status
            // if(!($response->json()['data']['face_data']['status'])){
            //     return errorJsonResponse(errors: [$response['verificationData']['face_data']['message'] ?? 'Selfie Verification Failed'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            // }

            return successfulJsonResponse($response->json()['data']);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'document verification failed.');
    }
}
