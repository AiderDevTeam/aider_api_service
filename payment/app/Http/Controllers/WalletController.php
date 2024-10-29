<?php

namespace App\Http\Controllers;

use App\Http\Actions\Wallet\SetDefaultWalletAction;
use App\Http\Actions\Wallet\ListWalletsAction;
use App\Http\Actions\Wallet\StoreWalletAction;
use App\Http\Actions\Wallet\UpdateWalletAction;
use App\Http\Requests\Wallet\SetDefaultWalletRequest;
use App\Http\Requests\Wallet\StoreWalletRequest;
use App\Http\Requests\Wallet\UpdateWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class WalletController extends Controller
{

    public function index(Request $authRequest, ListWalletsAction $listWalletsAction)
    {
        return $listWalletsAction->handle($authRequest);
    }

    public function store(Request $authRequest, StoreWalletRequest $walletRequest, StoreWalletAction $storeWalletAction): JsonResponse
    {
        return $storeWalletAction->handle($authRequest, $walletRequest);
    }

    public function update(UpdateWalletRequest $request, UpdateWalletAction $updateWalletAction): Response|JsonResponse|Application|ResponseFactory
    {
        return $updateWalletAction->handle($request);
    }

    public function setDefaultWallet(Wallet $wallet, SetDefaultWalletAction $setDefaultWalletAction): JsonResponse
    {
        return $setDefaultWalletAction->handle($wallet);
    }

}
