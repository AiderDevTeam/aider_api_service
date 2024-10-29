<?php

namespace App\Http\Actions\Delivery;

use App\Http\Requests\DeliveryFeeRequest;
use App\Http\Services\Delivery\ShaqExpressService;
use App\Http\Services\Delivery\WegooService;
use Exception;
use Illuminate\Http\JsonResponse;

class GetDeliveryFeeAction
{
    public function handle(DeliveryFeeRequest $request): JsonResponse
    {
        return self::shaqExpressDeliveryFee($request->validated());
    }

    public static function shaqExpressDeliveryFee(array $request): JsonResponse
    {
        try {
            if (($response = ShaqExpressService::getDeliveryFee($request)) && isset($response['data'])) {
                return successfulJsonResponse(data: [$response['data']]);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    public static function wegooDeliveryFee(array $request): JsonResponse
    {
        try {
            if ($response = WegooService::getExpressDeliveryFee($request)) {
                if (isset($response['data']['break_down'])) {
                    return successfulJsonResponse(data: [
                        'price' => $response['data']['break_down'][0]['price'],
                        'distance' => $response['data']['break_down'][0]['distance'],
                    ]);
                }
                logger('### Expected request parameters not found ###');
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
