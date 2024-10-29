<?php

namespace App\Actions\Order;

use App\Http\Requests\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Exception;
use Illuminate\Http\JsonResponse;

class ManualOrderUpdateAction
{
    public function handle(OrderUpdateRequest $request, Order $order): JsonResponse
    {
        try {
            $order->update(arrayKeyToSnakeCase($request->validated()));

            if ($request->has('status')) {
                $order->delivery->update(['status' => $request->validated('status')]);
            }

            return successfulJsonResponse(new OrderResource($order->load('orderCarts')));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
