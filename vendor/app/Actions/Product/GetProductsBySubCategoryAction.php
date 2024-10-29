<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\JsonResponse;

class GetProductsBySubCategoryAction
{
    public function handle(SubCategory $subCategory): JsonResponse
    {
        try {
            logger('## LOADING PRODUCTS FOR ' . strtoupper($subCategory->name) . ' CATEGORY ##');

            return successfulJsonResponse(
                ProductResource::collection(
                    $subCategory->eligibleProducts()
                        ->whereHas('photos')->where('products.quantity', '>', 0)
                        ->whereIn('products.status', [Status::ACTIVE, Status::PENDING])
                        ->with('vendor', 'tags', 'weightUnit', 'reviews.reviewable')->inRandomOrder()->simplepaginate(20)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }
}
