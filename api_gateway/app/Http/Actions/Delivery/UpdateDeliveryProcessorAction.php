<?php

namespace App\Http\Actions\Delivery;

use App\Http\Requests\UpdateDeliveryProcessorRequest;
use App\Http\Resources\DeliveryProcessorResource;
use App\Models\DeliveryProcessor;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateDeliveryProcessorAction
{
    public function handle(UpdateDeliveryProcessorRequest $request, DeliveryProcessor $deliveryProcessor): JsonResponse
    {
        logger('### UPDATING DELIVERY PROCESSOR ###');
        logger($request->validated());

        try {
            if ($deliveryProcessor->update(arrayKeyToSnakeCase($request->validated())))
                return successfulJsonResponse(new DeliveryProcessorResource($deliveryProcessor->refresh()));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
