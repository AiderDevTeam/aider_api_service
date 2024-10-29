<?php

namespace App\Http\Actions;

use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserPoyntBalanceAction
{
    public function handle(string $userExternalId): JsonResponse
    {
        try {
            if ($user = User::firstOrCreate(['external_id' => $userExternalId])) {
                return successfulJsonResponse(
                    data: ['poyntBalance' => $user->refresh()->poynt_balance],
                    message: 'Available poynt balance'
                );
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
