<?php

namespace App\Actions\Order;

use App\Custom\Status;
use App\Http\Requests\AcceptOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class AcceptOrderAction
{
    public function handle(AcceptOrderRequest $request): JsonResponse
    {
        try {
            if (Order::findWithExternalId($request->validated('orderExternalId'))->update([
                'is_accepted' => $request->validated('accepted'),
                'status' => $request->validated('accepted') ? Status::ACCEPTED : Status::DECLINED
            ])) {
                $message = $request->validated('accepted') ? 'order accepted. pending delivery.' : 'order declined';
                return successfulJsonResponse([], message: $message);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
