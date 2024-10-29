<?php

namespace App\Http\Actions\Wallet;

use App\Http\Resources\WalletResource;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ListWalletsAction
{
    public function handle(Request $authRequest): Response|JsonResponse|Application|ResponseFactory
    {
        logger()->info('### LOADING USER WALLETS ###');

        try {
            return successfulJsonResponse(WalletResource::collection(
                User::authUser($authRequest->user)->wallets()->with('bank')->orderBy('created_at', 'desc')->get()
            ));
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }

}
