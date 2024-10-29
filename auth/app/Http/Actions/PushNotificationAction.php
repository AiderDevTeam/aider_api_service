<?php

namespace App\Http\Actions;

use App\Http\Requests\PushNotificationRequest;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PushNotificationAction
{
    public function handle(User $user, PushNotificationRequest $request): JsonResponse
    {
        try {
            logger($request->validated());
            PushNotificationJob::dispatch($user, $request->validated())->onQueue('high');
            return successfulJsonResponse(statusCode: ResponseAlias::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
