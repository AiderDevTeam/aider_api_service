<?php

namespace App\Actions\DeliveryFee;

use App\Http\Requests\CreateDeliveryFeeRequest;
use App\Models\DeliveryFee;
use Illuminate\Http\JsonResponse;

class GetDeliveryFeeAction
{
    public function handle(): JsonResponse
    {
        try {
            return successfulJsonResponse(DeliveryFee::query()->get());
        } catch (\Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
