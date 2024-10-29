<?php

namespace App\Http\Actions\User;

use App\Http\Requests\UsernameSuggestionRequest;
use App\Http\Services\UsernameSuggestionService;
use App\Http\Services\Vendor\ShopService;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UsernameSuggestionAction
{
    public function handle(UsernameSuggestionRequest $request): JsonResponse
    {
        try {
            $request = $request->validated();

            if (!User::query()->where('username', $request['username'])->exists() && (new ShopService(['shopTag' => $request['username']]))->checkShopTagExistence())
                return successfulJsonResponse();

            return successfulJsonResponse(
                data: UsernameSuggestionService::generateUsername($request['firstName'], $request['lastName']),
                message: 'Username already taken'
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
