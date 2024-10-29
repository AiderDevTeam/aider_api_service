<?php

namespace App\Http\Actions\UserType;

use App\Http\Requests\UserTypeRequest;
use App\Models\UserType;
use Exception;
use Illuminate\Http\JsonResponse;

class StoreUserTypeAction
{
    public function handle(UserTypeRequest $request): JsonResponse
    {
        try {
            UserType::query()->create([
                'type' => $request->validated('type')
            ]);
            return successfulJsonResponse(message: 'User Type Stored', statusCode: 201);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
