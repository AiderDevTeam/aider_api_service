<?php

namespace App\Http\Actions\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreUserAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger("### STORING USER ###");

            if (isset($request->user['externalId'])) {
                $externalId = $request->user['externalId'];

                return successfulJsonResponse(new UserResource(
                    User::query()->updateOrCreate(['external_id' => $externalId])
                ));

            }
            return errorJsonResponse(message: 'User authorization failed', statusCode: 401);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
