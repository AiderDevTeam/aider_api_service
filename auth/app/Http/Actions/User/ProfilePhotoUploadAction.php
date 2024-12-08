<?php

namespace App\Http\Actions\User;

use App\Http\Requests\ProfilePhotoUploadRequest;
use App\Http\Services\API\FileUploadService;
use Exception;
use Illuminate\Http\JsonResponse;
use Cloudinary;

class ProfilePhotoUploadAction
{
    public function handle(ProfilePhotoUploadRequest $request): JsonResponse
    {
        logger('### UPDATING PROFILE PHOTO ###');
        try {

            $imageFile = Cloudinary::upload($request->file('profilePhoto')->getRealPath())->getSecurePath();
            if (auth()->user()->update(['profile_photo_url' => $imageFile]))
                return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Profile photo could not be updated. Try again again after sometime']);
    }

    private function getLocalPath(ProfilePhotoUploadRequest $request)
    {
        if ($request->has('profilePhoto') && !is_null($request->file('profilePhoto'))) {
            return getLocalPath($request->file('profilePhoto'));
        }
        return null;
    }
}
