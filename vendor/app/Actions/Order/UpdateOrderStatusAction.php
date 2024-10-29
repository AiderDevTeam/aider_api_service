<?php

namespace App\Actions\Order;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class UpdateOrderStatusAction
{
    public function handle(UpdateOrderStatusRequest $request):JsonResponse
    {
        try {
            logger($request);

                $order = Order::where('external_id', $request['externalId'])->first();
                $order->update(['status' => $request['status']]);
                $order->delivery->update(['tracking_number' => $request['trackingNumber']]);

                return successfulJsonResponse(
                    data: [],
                    message: 'Order Status Updated',
                    statusCode: 204
                );

        }catch (\Exception $exception){
            report($exception);
        }

        return errorJsonResponse();
    }

}
