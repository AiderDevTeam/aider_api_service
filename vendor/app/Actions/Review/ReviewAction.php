<?php

namespace App\Actions\Review;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\BookingProduct;
use App\Models\Cart;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewAction
{
    public function handle(Request $request, BookingProduct $bookingProduct, ReviewRequest $reviewRequest): JsonResponse
    {
        logger('### REVIEWING ' . $reviewRequest->validated('type') . ' ###');
        logger($requestPayload = $reviewRequest->validated());

        try {
            $user = User::authUser($request->user);

            if (!$bookingProduct->isReviewable())
                return errorJsonResponse(errors: ['You cannot review an uncompleted booking'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            $response = match ($requestPayload['type']) {
                Review::TYPES['PRODUCT'] => $this->reviewBookedProduct($bookingProduct, $user, $requestPayload),
                default => $this->reviewRenter($bookingProduct, $user, $requestPayload)
            };

            if (!$response['reviewed']) {
                return errorJsonResponse(errors: [$response['error']], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return successfulJsonResponse(new ReviewResource($response['data']));

        } catch (Exception $exception) {
            request($exception);
        }
        return errorJsonResponse();
    }

    private function reviewBookedProduct(BookingProduct $bookingProduct, Model $user, array $requestPayload): array
    {
        try {
            if ($bookingProduct->isReviewed())
                return ['reviewed' => false, 'error' => 'You have already reviewed this product in your booking'];

            if ($review = $bookingProduct->review()->create([
                'reviewer_id' => $user->id,
                'reviewee_id' => $bookingProduct->vendor->id,
                ...arrayKeyToSnakeCase($requestPayload)
            ])) {
                $bookingProduct->reviewed();
                return ['reviewed' => true, 'data' => $review];
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return ['reviewed' => false, 'error' => 'Product review cannot be completed at this time. Try again after sometime'];
    }

    private function reviewRenter(BookingProduct $bookingProduct, Model $user, array $requestPayload): array
    {
        try {
            $renter = $bookingProduct->renter()->first();
            if ($renter->renterReviews()->where('secondary_reviewable_id', $bookingProduct->id)->exists())
                return ['reviewed' => false, 'error' => 'You have already reviewed this user for this booking'];

            if ($review = $renter->renterReviews()->create([
                'reviewer_id' => $user->id,
                ...arrayKeyToSnakeCase($requestPayload),
                'secondary_reviewable_id' => $bookingProduct->id
            ])) {
                return ['reviewed' => true, 'data' => $review];
            }

        } catch (Exception $exception) {
            report($exception);
        }
        return ['reviewed' => false, 'error' => 'Renter review cannot be completed at this time. Try again after sometime'];
    }

}
