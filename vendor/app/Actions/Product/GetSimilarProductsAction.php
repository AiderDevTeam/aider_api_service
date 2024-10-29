<?php

namespace App\Actions\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetSimilarProductsAction
{
    public function handle(Request $request, Product $product): JsonResponse
    {
        logger("### LOADING SIMILAR PRODUCTS FOR PRODUCT [$product->external_id] ###");
        try {
            $halfOfUnitPrice = $product->unit_price / 2;
            $desiredSizes = explode(',', $product->size);

            $similarProducts = $product->subCategory->eligibleProducts()->whereNot('id', $product->id)
                ->whereBetween('unit_price', [
                    $halfOfUnitPrice,
                    ($product->unit_price + $halfOfUnitPrice)
                ])->where(function ($query) use ($desiredSizes) {
                    foreach ($desiredSizes as $size) {
                        $query->whereRaw("FIND_IN_SET(?, size)", [$size]);
                    }
                });

            logger("### SIMILAR PRODUCTS COUNT ###", [$similarProducts->count()]);

            if ($similarProducts->count() < 1) {
                logger('### ACTUAL SIMILAR PRODUCTS NOT FOUND, WIDENING  SCOPE ###');
                $similarProducts = $product->subCategory->eligibleProducts()->whereNot('id', $product->id);
            }

            return successfulJsonResponse(ProductResource::collection(
                $similarProducts->with('vendor', 'tags', 'weightUnit', 'reviews.reviewable')->inRandomOrder()->simplePaginate($request->dataPerPage ?? 20)
                    )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
