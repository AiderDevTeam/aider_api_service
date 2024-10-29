<?php

namespace App\Http\Controllers;

use App\Actions\Review\GetProductReviewsAction;
use App\Actions\Review\GetRenterReviewsAction;
use App\Actions\Review\GetVendorProductsReviewsAction;
use App\Actions\Review\GetVendorReviewsAction;
use App\Actions\Review\ReviewAction;
use App\Actions\Review\UpdateReviewAction;
use App\Http\Requests\ReviewRequest;
use App\Models\BookingProduct;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function review(Request $request, BookingProduct $bookingProduct, ReviewRequest $reviewRequest, ReviewAction $action): JsonResponse
    {
        return $action->handle($request, $bookingProduct, $reviewRequest);
    }

    public function reviewUpdate(Review $review, ReviewRequest $request, UpdateReviewAction $action): JsonResponse
    {
        return $action->handle($review, $request);
    }

    public function productReviews(Request $request, Product $product, GetProductReviewsAction $action): JsonResponse
    {
        return $action->handle($request, $product);
    }

    public function vendorReviews(Request $request, User $user, GetVendorReviewsAction $action): JsonResponse
    {
        return $action->handle($request, $user);
    }

    public function vendorProductReviews(Request $request, User $user, GetVendorProductsReviewsAction $action): JsonResponse
    {
        return $action->handle($request, $user);
    }

    public function renterReviews(Request $request, User $user, GetRenterReviewsAction $action): JsonResponse
    {
        return $action->handle($request, $user);
    }
}
