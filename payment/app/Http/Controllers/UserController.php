<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\ListUsersAction;
use App\Http\Actions\User\StoreUserAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function store(Request $authRequest, StoreUserAction $action): JsonResponse
    {
        return $action->handle($authRequest);
    }

    public function index(ListUsersAction $listUsersAction): JsonResponse|AnonymousResourceCollection
    {
        return $listUsersAction->handle();
    }
}
