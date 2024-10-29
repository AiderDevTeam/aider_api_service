<?php

namespace App\Http\Controllers;

use App\Http\Actions\PushNotificationAction;
use App\Http\Requests\PushNotificationRequest;
use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller
{
    public function send(PushNotificationRequest $request, PushNotificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
