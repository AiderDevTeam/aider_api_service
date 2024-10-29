<?php

namespace App\Http\Controllers;

use App\Http\Actions\SendOTPAction;
use App\Http\Requests\OTPRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    public function __invoke(OTPRequest $request, SendOTPAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
