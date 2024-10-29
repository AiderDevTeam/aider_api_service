<?php

namespace App\Actions\Review;

use App\Http\Resources\ProductResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetVendorProductsReviewsAction
{
    public function handle(Request $request, User $user): JsonResponse
    {
        logger("### LOADING VENDOR PRODUCTS WITH REVIEWS ###");
        try {

            return paginatedSuccessfulJsonResponse(
                ProductResource::collection(
                    $user->products()->whereHas('reviews')->with([
                        'reviews.reviewer.statistics', 'photos', 'prices',
                        'vendor.statistics', 'subCategoryItem', 'address', 'unavailableBookingDates'
                    ])
                        ->paginate($request->pageSize ?? 10)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
