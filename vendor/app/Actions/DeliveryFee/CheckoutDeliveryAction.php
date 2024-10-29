<?php

namespace App\Actions\DeliveryFee;

use App\Models\DeliveryFee;
use Illuminate\Http\JsonResponse;

class CheckoutDeliveryAction
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
