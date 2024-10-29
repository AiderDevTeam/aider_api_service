<?php

namespace App\Http\Actions\User;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\API\FileUploadService;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdateUserAction
{
    public function handle(UpdateUserRequest $request): JsonResponse
    {
        try {
            logger()->info('### UPDATING USER ###');
            $validatedData = $request->validated();


            $user = auth()->user();

            if ($request->filled('profilePhoto')) {
                if (!$file = self::uploadProfilePhoto($request->validated('profilePhoto')))
                    return errorJsonResponse(errors: ['Sorry profile photo cannot be updated at this time. Try again after sometime']);

                $validatedData['profilePhotoUrl'] = $file;
            }

            unset($validatedData['profilePhoto']);

            logger($validatedData);

            if ($user->update(arrayKeyToSnakeCase($validatedData))) {
                logger()->info('### USER UPDATED ###');

                if ($request->has('address'))
                    $this->updateAddress($user, $request->validated('address'));

                return successfulJsonResponse(data: new UserResource($user->refresh()), message: 'User data updated');
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private static function uploadProfilePhoto(string $base64String)
    {
        return FileUploadService::base64StringToFile($base64String);
    }

    private function updateAddress(User $user, array $address): void
    {
        if ($userAddress = $user->addresses()->first()) {
            $userAddress->update(arrayKeyToSnakeCase($address));
        } else {
            //create new address if user doesn't have an address
            $user->addresses()->create(arrayKeyToSnakeCase($address));
        }
    }
}
