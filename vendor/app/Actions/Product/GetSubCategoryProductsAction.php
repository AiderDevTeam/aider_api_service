<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;

class GetSubCategoryProductsAction
{
    public function handle(Category $category): JsonResponse
    {
        try {
            logger('### FETCHING PRODUCTS FOR SEE ALL PAGE ###');
            return successfulJsonResponse(
                ProductResource::collection($category->products()->whereHas('photos')->with('vendor', 'tags', 'weightUnit', 'reviews.reviewable')->inRandomOrder()->limit(60)->get())
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }
}
