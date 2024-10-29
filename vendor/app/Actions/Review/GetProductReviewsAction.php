<?php

namespace App\Actions\Review;

use App\Http\Resources\ReviewResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetProductReviewsAction
{
    public function handle(Request $request, Product $product): JsonResponse
    {
        logger("### LOADING REVIEWS FOR PRODUCT [$product->external_id] ###");
        try {
            return paginatedSuccessfulJsonResponse(
                ReviewResource::collection($product->reviews()->with('reviewer.statistics')
                    ->paginate($request->pageSize ?? 10))
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
