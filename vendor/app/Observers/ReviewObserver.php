<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "creating" event.
     */
    public function creating(Review $review): void
    {
        $review->external_id = uniqid('REV');
    }

    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        if ($review->isBookingProductReview()) {
            $review->reviewable->vendor->recordVendorAverageRating();
            $review->reviewable->product->recordAverageRating();
        }

        if ($review->isRenterReview()) {
            $review->reviewable->recordRenterAverageRating();
        }
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
//        if ($review->isBookingProductReview() && $review->isDirty('rating')) {
//            $review->reviewable->vendor->recordAverageRating();
//            $review->reviewable->product->recordAverageRating();
//        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
