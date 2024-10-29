<?php

namespace App\Http\Actions;

use App\Http\Requests\PushNotificationRequest;
use App\Http\Services\PushNotificationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PushNotificationAction
{
    public function handle(PushNotificationRequest $request): JsonResponse
    {
        try{
            PushNotificationService::send(
                $request->validated('pushNotificationToken'),
                $request->validated('title'),
                $request->validated('body'),
                $request->validated('data'),
            );
            return successfulJsonResponse(statusCode: Response::HTTP_NO_CONTENT);
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
