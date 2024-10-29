<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Requests\DeleteProductRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeleteProductAction
{
    public function handle(Product $product): JsonResponse
    {
        try {
            logger("### DELETING PRODUCT [$product->external_id] ###");

            $product->update(['status' => Product::INACTIVE]);
            $product->delete();

            return successfulJsonResponse(data: [], message: 'Product Deleted', statusCode: 204);
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
