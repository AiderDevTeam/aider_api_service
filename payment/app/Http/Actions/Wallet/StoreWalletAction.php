<?php

namespace App\Http\Actions\Wallet;

use App\Events\PayoutWalletEvent;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StoreWalletAction
{
    public function handle(Request $authRequest, StoreWalletRequest $request): JsonResponse
    {
        logger()->info('### CREATING WALLET ###');
        logger()->info($payload = $request->validated());

        try {
            $user = User::authUser($authRequest->user);

            if ($user->hasAddedThisAccountNumber($payload['accountNumber']))
                return errorJsonResponse(errors: ['This account number has already been added to your wallet'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            return successfulJsonResponse(
                new WalletResource(
                    $user->wallets()->create(arrayKeyToSnakeCase($payload))->refresh()->load('bank')
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
