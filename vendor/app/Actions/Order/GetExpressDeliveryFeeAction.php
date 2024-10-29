<?php

namespace App\Actions\Order;

use App\Http\Requests\ExpressDeliveryFeeRequest;
use App\Http\Services\Api\DeliveryService;
use App\Models\Cart;
use Exception;
use Illuminate\Http\JsonResponse;

class GetExpressDeliveryFeeAction
{
    public function handle(ExpressDeliveryFeeRequest $request): JsonResponse
    {
        try {
            $requestData = $request->validated();
            $carts = Cart::whereIn('external_id', $requestData['cartExternalIds'])->get();
            $vendorAddress = $carts->first()->vendor->address;

            $cartItems = $carts->map(function ($cart) {
                return [
                    'name' => $cart->product->name,
                    'type' => $cart->product->subCategory->name,
                    'quantity' => $cart->product->quantity,
                    'addInsurance' => false,
                    'price' => $cart->product->unit_price,
                    'weight' => $cart->product->weight,
                    'isFragile' => false
                ];
            })->toArray();

            $deliveryFeeRequestData = [
                ...$requestData,
                'originName' => $vendorAddress->origin_name ?? $vendorAddress->city,
                'originCountry' => 'Ghana',
                'originState' => $vendorAddress->city,
                'originLatitude' => $vendorAddress->latitude,
                'originLongitude' => $vendorAddress->longitude,
                'items' => $cartItems
            ];

            if ($response = DeliveryService::getExpressDeliveryFee($deliveryFeeRequestData)) {
                return successfulJsonResponse(data: $response['data']);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['Express delivery unavailable at this time'], statusCode: 422);
    }
}
