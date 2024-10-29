<?php

namespace App\Http\Actions\Wallet;

use App\Http\Requests\Wallet\SetDefaultWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;


class SetDefaultWalletAction
{
    public function handle(Wallet $wallet): JsonResponse
    {
        try {
            $wallet->user->wallets()->update(['default' => false]);
            $wallet->setAsDefault();

            return successfulJsonResponse(new WalletResource($wallet->refresh()->load('bank')));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
