<?php

namespace App\Http\Controllers;

use App\Http\Actions\UserType\GrantUserTypeToUsersAction;
use App\Http\Actions\UserType\StoreUserTypeAction;
use App\Http\Actions\UserType\UpdateUsersUserTypeAction;
use App\Http\Requests\GrantUserTypeToUsersRequest;
use App\Http\Requests\UpdateUsersUserTypeRequest;
use App\Http\Requests\UserTypeRequest;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\JsonResponse;

class UserTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return successfulJsonResponse(UserType::all('id', 'type'), 'User Types');
    }

    public function store(UserTypeRequest $request, StoreUserTypeAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function updateUsersUserType(UpdateUsersUserTypeRequest $request, UpdateUsersUserTypeAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function grantUserType(GrantUserTypeToUsersRequest $request, GrantUserTypeToUsersAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
