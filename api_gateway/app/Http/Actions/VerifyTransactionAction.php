<?php

namespace App\Http\Actions;

use App\Http\Services\PaystackService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyTransactionAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger($request['reference']);
            if (!isset($request['reference'])) {
                return errorJsonResponse(errors: ['transaction reference is required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($response = PaystackService::verifyTransaction($request['reference']))
                return successfulJsonResponse($response['data']);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Transaction reference not found']);
    }
}
