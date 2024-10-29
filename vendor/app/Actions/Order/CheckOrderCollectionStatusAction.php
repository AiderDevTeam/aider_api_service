<?php

namespace App\Actions\Order;

use App\Http\Requests\CheckOrderCollectionStatusRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class CheckOrderCollectionStatusAction
{
    public function handle(CheckOrderCollectionStatusRequest $request): JsonResponse
    {
        try {
            logger('### CHECKING ORDER COLLECTION STATUS ###');

            $order = Order::where('external_id' , $request->orderExternalId)->first();

            return successfulJsonResponse(
                data: $order->collection_status,
                message: "Order Collection Status"
            );
        } catch (\Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
