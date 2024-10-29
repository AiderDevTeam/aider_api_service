<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rentedItemsCount' => (int)$this->rented_items_count,
            'listedItemsCount' => (int)$this->listed_items_count,
            'vendorAverageRating' => (float)$this->vendor_average_rating,
            'renterAverageRating' => (float)$this->renter_average_rating,
            'vendorReviewsCount' => (int)$this->vendor_reviews_count,
            'renterReviewsCount' => (int)$this->renter_reviews_count,
            'pendingVendorBookingsCount' => (int)$this->vendor_bookings_pending_pickup_count,
            'pendingRenterBookingsCount' => (int)$this->renter_bookings_pending_pickup_count,
            'vendorBookingsPendingAcceptanceCount' => (int)$this->vendor_bookings_pending_acceptance_count,
            'vendorIndividualRatingCounts' => $this->vendor_individual_rating_counts,
            'renterIndividualRatingCounts' => $this->renter_individual_rating_counts
        ];
    }
}
