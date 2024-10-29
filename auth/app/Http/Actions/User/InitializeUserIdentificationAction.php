<?php

namespace App\Http\Actions\User;

use App\Custom\Identification;
use App\Http\Requests\UserIdentificationRequest;
use App\Http\Resources\UserIdentificationResource;
use App\Models\UserIdentification;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
                    return errorJsonResponse(errors: ['This id number has been used by another user for verification'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                if ($this->idNumberHasBeenUsedByUserForDifferentVerification($user, $payload)) {
                    return errorJsonResponse(errors: ['You have already used this id number for a different verification'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $data = [
                'id_number' => $payload['idNumber'] ?? null,
                'type' => $payload['type'],
                'status' => Identification::STATUS['PENDING']
            ];

            if ($userIdentification = $user->identifications()->where('type', $payload['type'])->first()) {
                $userIdentification->update($data);
            } else {
                $userIdentification = $user->identifications()->create($data);
            }

            $userIdentification->process($this->getLocalPaths($request));

            return successfulJsonResponse(
                data: new UserIdentificationResource($userIdentification)
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Sorry KYC cannot be complete at this time. Please try again after sometime.']);
    }

    private function idNumberUsedByAnotherUser(string $idNumber, int $userId): bool
    {
        return UserIdentification::query()->where('id_number', $idNumber)
            ->where('status', Identification::STATUS['ACCEPTED'])
            ->whereNot('user_id', $userId)->exists();
    }

    private function idNumberHasBeenUsedByUserForDifferentVerification(Model $user, array $data)
    {
        return $user->identifications()->where('id_number', $data['idNumber'])->whereNot('type', $data['type'])->exists();
    }

    private function getLocalPaths(UserIdentificationRequest $request): array
    {
        try {
            $selfie = null;
            $docImage = null;

            if ($request->has('selfie') && !is_null($request->file('selfie'))) {
                $selfie = $request->file('selfie')->store('public/uploads');
            }

            if ($request->has('documentImage') && !is_null($request->file('documentImage'))) {
                $docImage = $request->file('documentImage')->store('public/uploads');
            }

        } catch (Exception $exception) {
            report($exception);
        }

        return ['selfie' => $selfie, 'documentImage' => $docImage];
    }
}
