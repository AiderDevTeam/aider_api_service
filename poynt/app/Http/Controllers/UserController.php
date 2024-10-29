<?php

namespace App\Http\Controllers;

use App\Http\Actions\GetUserPoyntBalanceAction;
use App\Http\Actions\UpdateUserPoyntAction;
use App\Http\Requests\UpdateUserPoyntRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getpoyntBalance(string $userExternalId, GetUserPoyntBalanceAction $action): JsonResponse
    {
        return $action->handle($userExternalId);
    }

    public function updateUserPoynt(string $userExternalId, UpdateUserPoyntRequest $request, UpdateUserPoyntAction $action): JsonResponse
    {
        return $action->handle($userExternalId, $request);
    }

}
