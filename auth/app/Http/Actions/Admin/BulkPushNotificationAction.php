<?php

namespace App\Http\Actions\Admin;

use App\Events\BulkPushNotificationEvent;
use App\Http\Requests\PushNotificationRequest;
use App\Jobs\BulkPushNotificationJob;
use Exception;
use Illuminate\Http\JsonResponse;

class BulkPushNotificationAction
{
    public function handle(PushNotificationRequest $request): JsonResponse
    {
        try {
            BulkPushNotificationJob::dispatch($request->validated())->onQueue('batch');
            return successfulJsonResponse();
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
