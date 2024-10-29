<?php

namespace App\Http\Actions;

use App\Http\Requests\SmsNotificationRequest;
use App\Jobs\SmsNotificationJob;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SendSmsNotificationAction
{
    public function handle(SmsNotificationRequest $request): JsonResponse
    {
        try {
            logger($data = $request->validated());
            $user = $request->has('userExternalId') ? User::findWithExternalId($data['userExternalId']) : null;
            SmsNotificationJob::dispatch(
                $user,
                $data['phone'] ?? null,
                $data['message'],
            )->onQueue('high');
            return successfulJsonResponse(statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
