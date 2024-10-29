<?php

namespace App\Http\Actions\Wallet;

use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use Exception;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateWalletAction
{
    public function handle(Request $request): JsonResponse
    {
        try {

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

}
