<?php

namespace App\Http\Actions\Delivery;

use App\Http\Services\Delivery\WegooService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteDeliveryAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            if (!$request->has('trackingNumber'))
                return errorJsonResponse(['tracking number required'], Response::HTTP_UNPROCESSABLE_ENTITY);

            $response = WegooService::deleteDelivery($request->trackingNumber);
            return successfulJsonResponse(data: $response);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
