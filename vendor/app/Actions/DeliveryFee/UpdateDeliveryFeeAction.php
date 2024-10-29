<?php

namespace App\Actions\DeliveryFee;

use App\Http\Requests\UpdateDeliveryFeeRequest;
use App\Models\DeliveryFee;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateDeliveryFeeAction
{
    public function handle(UpdateDeliveryFeeRequest $request, DeliveryFee $deliveryFee): JsonResponse
    {
        try {
            logger($request);
            $deliveryFee->update(arrayKeyToSnakeCase($request->validated()));

            return successfulJsonResponse(data: $deliveryFee->refresh());
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
