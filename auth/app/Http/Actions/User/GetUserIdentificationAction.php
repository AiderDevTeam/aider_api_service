<?php

namespace App\Http\Actions\User;

use App\Http\Resources\UserIdentificationResource;
use App\Models\UserIdentification;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class GetUserIdentificationAction
{
    
    public function handle(Request $request): JsonResponse
    {
        logger('### FETCHING USER IDENTIFICATION ###');

        try {
            $user = auth()->user();
            // dd($user);
            return successfulJsonResponse(UserIdentificationResource::collection($user->identifications));

        } catch (Exception $exception) {
            report($exception);
            return errorJsonResponse(errors: ['Sorry, User KYC cant be fetched, Please try again later.']);
        }
    }
}
