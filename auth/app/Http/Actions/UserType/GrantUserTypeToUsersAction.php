<?php

namespace App\Http\Actions\UserType;

use App\Http\Requests\GrantUserTypeToUsersRequest;
use App\Models\User;
use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;

class GrantUserTypeToUsersAction
{
    public function handle(GrantUserTypeToUsersRequest $request): JsonResponse
    {
        logger('### GRANTING USER TYPE ACCESS TO USERS ###');
        logger($request);

        try {
            foreach (User::whereIn('username', $request->validated('usernames'))->get() as $user) {
                logger("### GRANTING [$request->userType] USER TYPE TO [$user->username] ###");

                $user->userTypes()->syncWithoutDetaching(
                    UserType::getUserType(
                        strtolower($request->validated('userType'))
                    )?->id
                );
            }
            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
