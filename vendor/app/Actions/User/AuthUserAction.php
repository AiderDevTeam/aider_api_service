<?php

namespace App\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthUserAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            User::authUser($request->user);
            return successfulJsonResponse([]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
