<?php

namespace App\Http\Actions\Delivery;

use App\Http\Services\Delivery\WegooService;
use Exception;

class GetWegooDeliveryByTrackingNumberAction
{
    public function handle(string $trackingNumber)
    {
        try {
            return WegooService::getDeliveryByTrackingNumber($trackingNumber);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
