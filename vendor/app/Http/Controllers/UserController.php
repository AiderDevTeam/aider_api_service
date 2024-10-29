<?php

namespace App\Http\Controllers;

use App\Actions\User\AuthUserAction;
use App\Actions\User\GetUserLikedProductsAction;
use App\Actions\User\GetUserShopsAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateUser(User $user, UpdateUserRequest $request, UpdateUserAction $action): JsonResponse
    {
        return $action->handle($user, $request);
    }

    public function getUserShops(User $user, GetUserShopsAction $action): JsonResponse
    {
        return $action->handle($user);
    }

    public function getLikedProducts(Request $request, User $user, GetUserLikedProductsAction $action): JsonResponse
    {
        return $action->handle($request, $user);
    }

    public function getAuthUser(Request $request, AuthUserAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
