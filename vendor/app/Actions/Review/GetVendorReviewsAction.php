<?php

namespace App\Actions\Review;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ReviewResource;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetVendorReviewsAction
{
    public function handle(Request $request, User $user): JsonResponse
    {
        logger("### LOADING REVIEWS FOR VENDOR [$user->external_id] ###");
        try {
            return paginatedSuccessfulJsonResponse(
                ReviewResource::collection(
                    $user->vendorReviews()->with('reviewer.statistics')->paginate($request->pageSize ?? 10)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
