<?php

namespace App\Actions\DeliveryFee;

use App\Http\Requests\CreateDeliveryFeeRequest;
use App\Models\DeliveryFee;
use Illuminate\Http\JsonResponse;

class CreateDeliveryFeeAction
{
    public function handle(CreateDeliveryFeeRequest $request): JsonResponse
    {
        try {
            $deliveryFee = DeliveryFee::query()->create(arrayKeyToSnakeCase($request->validated()));
            return successfulJsonResponse(data: $deliveryFee, message: 'Delivery Fee Created', statusCode: 201);
        } catch (\Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
