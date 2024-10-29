<?php

namespace App\Actions\User;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateUserAction
{
    public function handle(User $user, UpdateUserRequest $request): JsonResponse
    {
        try {
            if ($user->update(arrayKeyToSnakeCase($request->validated()))) {
                return successfulJsonResponse([]);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
