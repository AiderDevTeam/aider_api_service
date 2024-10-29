<?php

namespace App\Actions\Product;

use App\Http\Requests\ProductPriceDeleteRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeleteProductPriceAction
{
    public function handle(Product $product, ProductPriceDeleteRequest $request): JsonResponse
    {
        logger('### DELETING PRODUCT PRICE ###');
        logger($request);

        try {
            $product->prices()->find($request->validated('productPriceId'))->delete();
            return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
