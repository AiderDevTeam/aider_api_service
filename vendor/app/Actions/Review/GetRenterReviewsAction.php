<?php

namespace App\Actions\Review;

use App\Http\Resources\ReviewResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetRenterReviewsAction
{
    public function handle(Request $request, User $user): JsonResponse
    {
        try {
            logger('### LOADING RENTER\'S REVIEWS ###');

            return paginatedSuccessfulJsonResponse(
                ReviewResource::collection($user->renterReviews()->with('reviewer.statistics')
                    ->paginate($request->pageSize ?? 10)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
