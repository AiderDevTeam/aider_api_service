<?php

namespace App\Http\Actions\User;

use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutAction
{
    public function handle(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return successfulJsonResponse(statusCode: 204);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
