<?php

namespace App\Actions\Order;

use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateOrderAction
{
    public function handle(UpdateOrderRequest $orderRequest): JsonResponse
    {
        try {
            logger($orderRequest);
            if($order = Order::where('external_id', $orderRequest['deliveryExternalId'])->update([
                'collection_status' => $orderRequest['collectionStatus']
            ])){
                return successfulJsonResponse(
                    data: $order,
                    message: 'Order Updated',
                    statusCode: 204
                );
            }
        }catch (\Exception $exception){
            report($exception);
        }

        return errorJsonResponse();
    }
}
