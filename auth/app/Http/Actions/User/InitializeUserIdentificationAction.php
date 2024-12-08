<?php

namespace App\Http\Actions\User;

use App\Http\Services\PremblyService;
use App\Custom\Identification;
use App\Http\Requests\UserIdentificationRequest;
use App\Http\Resources\UserIdentificationResource;
use App\Models\UserIdentification;
use App\Http\Services\API\PremblyKYCService;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Actions\Verification\IdNumberAndFaceVerificationAction;
use App\Http\Requests\IdNumberAndFaceVerificationRequest;
use Cloudinary;

class InitializeUserIdentificationAction
{
    
    public function handle(UserIdentificationRequest $request): JsonResponse
    {
        logger('### INITIALIZING USER IDENTIFICATION ###');

        try {
            $payload = $request->validated();
            $user = auth()->user();

            if ($user->hasCompletedKYC()) {
                return successfulJsonResponse(message: 'You have completed KYC.');
            }

            if (isset($payload['idNumber'])) {
                if ($this->idNumberUsedByAnotherUser($payload['idNumber'], $user->id)) {
                    return errorJsonResponse(errors: ['This ID number has been used by another user for verification'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($this->idNumberHasBeenUsedByUserForDifferentVerification($user, $payload)) {
                    return errorJsonResponse(errors: ['You have already used this ID number for a different verification'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            return $this->InitiateVerification($payload);

        } catch (Exception $exception) {
            report($exception);
            return errorJsonResponse(errors: ['Sorry, KYC cannot be completed at this time. Please try again later.']);
        }
    }

    private function InitiateVerification(array $data): JsonResponse
    {
        logger('### ID NUMBER WITH FACE VERIFICATION ACTION ###');
        try {
            $response = match ($data['type']) {
                'nin' => $this->verifyNIN($data),
                'bvn' => $this->verifyBVN($data),
                default => null
            };

            if (is_null($response)) {
                return errorJsonResponse(errors: ['Invalid verification type'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$response['verified']) {
                return errorJsonResponse(errors: [$response['error']], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return successfulJsonResponse(message: $response['message'], data: $response['verificationData']);

        } catch (Exception $exception) {
            report($exception);
            return errorJsonResponse(errors: ['Verification process failed.']);
        }
    }

    private function verifyBVN(array $data): array
    {
        $user = auth()->user();
        $verifyData = [
            'number' => $data['idNumber'] ?? null,
        ];
        $response = (new PremblyService($verifyData))->verifyBVN();

        if (
            is_null($response) || !isset($response->json()['status'])
            || !isset($response->json()['data'])
            || !$response->json()['status']
        ) {
            return ['verified' => false, 'error' => 'BVN verification failed'];
        }
        $responseData = $response->json()['data'];

        $selfieUrl = Cloudinary::upload($data['selfie']->getRealPath())->getSecurePath();        
        unset($responseData['base64Image']);
        
        $newBody = [
            'user_id' => $user->id,
            'id_number' => $data['idNumber'] ?? null,
            'type' => $data['type'],
            'selfie_url' => $selfieUrl,
            'status' => Identification::STATUS['ACCEPTED'],
            'verification_details' => $responseData
        ];
        
        $userIdentification = $user->identifications()->create($newBody);
        $user->update([
            'id_verification_status' => 'completed',
            'id_verified' => true,
            'id_verified_at' => now()
        ]);
        return ['verified' => true, 'message'=> 'BVN verification is Successful.', 'verificationData' => new UserIdentificationResource($userIdentification)];
    }

    private function verifyNIN(array $data): array
    {
        $user = auth()->user();
        $verifyData = [
            'number' => $data['idNumber'] ?? null,
        ];
        $response = (new PremblyService($verifyData))->verifyNIN();

        if (
            is_null($response) || !isset($response->json()['status'])
            || !isset($response->json()['nin_data'])
            || !$response->json()['status']
        ) {
            return ['verified' => false, 'error' => 'NIN verification failed'];
        }
        $responseData = $response->json()['nin_data'];

        $selfieUrl = Cloudinary::upload($data['selfie']->getRealPath())->getSecurePath();        
        unset($responseData['base64Image']);
        
        $newBody = [
            'user_id' => $user->id,
            'id_number' => $data['idNumber'] ?? null,
            'type' => $data['type'],
            'selfie_url' => $selfieUrl,
            'status' => Identification::STATUS['ACCEPTED'],
            'verification_details' => $responseData
        ];
        
        $userIdentification = $user->identifications()->create($newBody);

        $user->update([
            'id_verification_status' => 'completed',
            'id_verified' => true,
            'id_verified_at' => now()
        ]);

        return ['verified' => true, 'message'=> 'NIN verification is Successful.', 'verificationData' => new UserIdentificationResource($userIdentification)];
    }

    private function idNumberUsedByAnotherUser(string $idNumber, int $userId): bool
    {
        return UserIdentification::query()
            ->where('id_number', $idNumber)
            ->where('status', Identification::STATUS['ACCEPTED'])
            ->whereNot('user_id', $userId)
            ->exists();
    }

    private function idNumberHasBeenUsedByUserForDifferentVerification(Model $user, array $data): bool
    {
        return $user->identifications()
            ->where('id_number', $data['idNumber'])
            ->whereNot('type', $data['type'])
            ->exists();
    }


    private function verifyDocument(): void
    {
        logger('### PROCESSING DOCUMENT VERIFICATION ###');

        try {

            $this->userIdentification->updateQuietly([
                'document_url' => FileUploadService::uploadToImageService($this->data['documentImage']),
                'selfie_url' => FileUploadService::uploadToImageService($this->data['selfie'])
            ]);

            $response = (new PremblyKYCService([
                'docType' => $this->userIdentification->formatType(),
                'docCountry' => 'NG',
                'docImage' => $this->data['base64Selfie'],
                'selfieImage' => $this->data['base64DocumentImage']
            ]))->verifyDocument();

            if (is_null($response) || !$response->successful()) {
                $this->userIdentification->reject();
                $this->userIdentification->rejectionReasons()->create(['reason' => json_decode($response, true)['errors'][0] ?? 'Document Verification Failed']);
                return;
            }

            $this->userIdentification->update([
                'status' => Identification::STATUS['ACCEPTED'],
                'verification_details' => $response->json()['data']
            ]);

        } catch (Exception $exception) {
            $this->userIdentification->reject();
            report($exception);
        }
    }
}
