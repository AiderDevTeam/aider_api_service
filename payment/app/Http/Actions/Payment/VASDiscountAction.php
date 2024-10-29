<?php

namespace App\Http\Actions\Payment;

use App\Http\Requests\VASDiscountRequest;
use App\Models\VASDiscount;
use Exception;
use Illuminate\Http\JsonResponse;

class VASDiscountAction
{
    public function handle(VASDiscountRequest $request): JsonResponse
    {
        try {
            VASDiscount::query()->updateOrCreate(
                [
                    'type' => $request->validated('type')],
                [
                    'discount' => $request->validated('discount')
                ]);
            return successfulJsonResponse(data: VASDiscount::all());
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
