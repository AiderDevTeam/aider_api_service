<?php

namespace App\Http\Actions;

use App\Http\Requests\UpdateUserPoyntRequest;
use App\Models\User;
use App\Models\UserActionPoynt;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateUserPoyntAction
{
    public function handle(string $userExternalId, UpdateUserPoyntRequest $request): JsonResponse
    {
        try {
            if ($user = User::firstOrCreate(['external_id' => $userExternalId])) {
                return $request->validated('type') === UserActionPoynt::ACTION_POYNT_TYPES['CREDIT'] ?
                    self::creditUserPoynt($user, $request) : self::debitUserPoynt($user, $request);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private static function creditUserPoynt(User $user, UpdateUserPoyntRequest $request): JsonResponse
    {
        $actionValue = $request->has('actionValue') ? $request->validated('actionValue') : 1;

        if ($user->creditPoynt(
            $request->validated('action'),
            $request->validated('actionResponsePayload'),
            $actionValue
        )) {
            return successfulJsonResponse([
                'poyntBalance' => $user->refresh()->poynt_balance,
            ], message: 'Poynt balance credited successfully');
        }
        return errorJsonResponse();
    }

    private static function debitUserPoynt(User $user, UpdateUserPoyntRequest $request): JsonResponse
    {
        if ($request->validated('debitPoynt') > $user->poynt_balance)
            return errorJsonResponse(message: 'Insufficient poynt balance', statusCode: 402);

        if ($user->debitPoynt(
            $request->validated('debitPoynt'),
            $request->validated('actionResponsePayload'))) {
            return successfulJsonResponse([
                'poyntBalance' => $user->refresh()->poynt_balance,
            ], message: 'Poynt balance debited successfully');
        }
        return errorJsonResponse();
    }
}
