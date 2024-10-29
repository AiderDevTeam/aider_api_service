<?php

namespace App\Actions\User;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserLikedProductsAction
{
    public function handle(Request $request, User $user): JsonResponse
    {
        logger("### LOADING LIKED PRODUCTS FOR USER [$user->external_id] ###");
        try {
            $likedProducts = $user->likedProducts()->orderBy('product_likes.updated_at', 'DESC')
                ->wherePivot('unliked', false)->whereIn('status', [Status::ACTIVE, Status::PENDING])
                ->whereHas('photos')->with(['vendor', 'tags', 'reviews.reviewable', 'subCategory'])
                ->simplePaginate($request->dataPerPage ?? 20);

            return successfulJsonResponse(ProductResource::collection($likedProducts));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
