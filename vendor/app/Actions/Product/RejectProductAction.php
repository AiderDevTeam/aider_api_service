<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Requests\RejectProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class RejectProductAction
{
    public function handle(RejectProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product->rejectionReasons()->create(['reason' => $request->validated('reason')]);
            $product->update(['status' => Status::INACTIVE]);

            return successfulJsonResponse(data: $product->refresh(), message: 'Product Rejection Reason Created', statusCode: 201);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
