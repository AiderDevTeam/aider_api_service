<?php

namespace App\Http\Actions\Delivery;

use App\Http\Requests\StoreDeliveryProcessorRequest;
use App\Http\Resources\DeliveryProcessorResource;
use App\Models\DeliveryProcessor;
use Exception;
use Illuminate\Http\JsonResponse;

class StoreDeliveryProcessorAction
{
    public function handle(StoreDeliveryProcessorRequest $request): JsonResponse
    {
        try {
            logger('### CREATING DELIVERY PROCESSOR ###');
            logger($request->validated());

            if (DeliveryProcessor::create([
                'external_id' => uniqid('DP'),
                ...arrayKeyToSnakeCase($request->validated())])
            )
                return successfulJsonResponse(data: DeliveryProcessorResource::collection(DeliveryProcessor::all()));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
