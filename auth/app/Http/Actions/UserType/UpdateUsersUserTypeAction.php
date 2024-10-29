<?php

namespace App\Http\Actions\UserType;

use App\Http\Requests\UpdateUsersUserTypeRequest;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdateUsersUserTypeAction
{
    public function handle(UpdateUsersUserTypeRequest $request): JsonResponse
    {
        try {
            $userTypeIds = UserType::whereIn('type', $request->validated('userType'))->pluck('id');
            auth()->user()->userTypes()->syncWithoutDetaching($userTypeIds);

            return successfulJsonResponse(statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
