<?php

namespace App\Http\Controllers;

use App\Http\Actions\Admin\BulkPushNotificationAction;
use App\Http\Actions\PushNotificationAction;
use App\Http\Actions\SendSmsNotificationAction;
use App\Http\Requests\PushNotificationRequest;
use App\Http\Requests\SmsNotificationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function pushNotification(User $user, PushNotificationRequest $request, PushNotificationAction $action): JsonResponse
    {
        return $action->handle($user, $request);
    }

    public function sendSmsNotification(SmsNotificationRequest $request, SendSmsNotificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function sendBulkPushNotification(PushNotificationRequest $request, BulkPushNotificationAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
