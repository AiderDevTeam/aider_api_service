<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\InitializeUserIdentificationAction;
use App\Http\Actions\User\GetUserIdentificationAction;
use App\Http\Requests\UserIdentificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserIdentificationController extends Controller
{
    public function initialize(UserIdentificationRequest $request, InitializeUserIdentificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
    
    public function getUserKYC(Request $request, GetUserIdentificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
