<?php

namespace App\Http\Actions\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;

class ListUsersAction
{
    public function handle(): JsonResponse
    {
        try {
            return successfulJsonResponse(UserResource::collection(User::all()));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }


}

