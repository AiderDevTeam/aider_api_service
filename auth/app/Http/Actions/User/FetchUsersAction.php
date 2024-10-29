<?php

namespace App\Http\Actions\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FetchUsersAction
{
    public function handle(Request $request): JsonResponse
    {
        try {

            return paginatedSuccessfulJsonResponse(
                UserResource::collection(
                    User::query()->with(
                        'identifications', 'addresses'
                    )->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
