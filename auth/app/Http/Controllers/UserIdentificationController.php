<?php

namespace App\Http\Controllers;

use App\Http\Actions\User\InitializeUserIdentificationAction;
use App\Http\Requests\UserIdentificationRequest;
use Illuminate\Http\JsonResponse;

class UserIdentificationController extends Controller
{
    public function initialize(UserIdentificationRequest $request, InitializeUserIdentificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
