<?php

namespace App\Http\Controllers;

use App\Http\Actions\SmsAction;
use App\Http\Requests\SmsRequest;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    public function __invoke(SmsRequest $request, SmsAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
