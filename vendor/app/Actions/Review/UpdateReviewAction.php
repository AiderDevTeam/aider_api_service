<?php

namespace App\Actions\Review;

use App\Http\Requests\ReviewRequest;
use App\Models\Review;
use Exception;
use Illuminate\Http\Response;

class UpdateReviewAction
{
    public function handle(Review $review, ReviewRequest $request)
    {
        logger()->info("### UPDATING REVIEW [$review->external_id] ###");
        logger($request);
        try {

            if ($review->update(arrayKeyToSnakeCase($request->validated())))
                return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
