<?php

namespace App\Http\Actions;

use App\Http\Requests\SmsRequest;
use App\Http\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mockery\Exception;

class SmsAction
{
    public function handle(SmsRequest $request): JsonResponse
    {
        try {
            SmsService::send(
                $request->validated('to'),
                $request->validated('message'),
                $request->validated('from') ?? null);
            return successfulJsonResponse(statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
