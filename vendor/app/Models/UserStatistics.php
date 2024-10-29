<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class UserStatistics extends Model
{
    use HasFactory;

    protected $table = 'user_statistics';

    protected $fillable = [
        'user_id',
        'rented_items_count',
        'listed_items_count',
        'vendor_average_rating',
        'renter_average_rating',
        'vendor_reviews_count',
        'renter_reviews_count',
        'vendor_bookings_pending_pickup_count',
        'renter_bookings_pending_pickup_count',
        'vendor_bookings_pending_acceptance_count',
        'vendor_individual_rating_counts',
        'renter_individual_rating_counts',
    ];

    public function setVendorIndividualRatingCountsAttribute(?array $vendorIndividualRatingCount): void
    {
        $this->attributes['vendor_individual_rating_counts'] = json_encode($vendorIndividualRatingCount);
    }

    public function getVendorIndividualRatingCountsAttribute()
    {
        return json_decode($this->attributes['vendor_individual_rating_counts'], true);
    }

    public function setRenterIndividualRatingCountsAttribute(?array $renterIndividualRatingCount): void
    {
        $this->attributes['renter_individual_rating_counts'] = json_encode($renterIndividualRatingCount);
    }

    public function getRenterIndividualRatingCountsAttribute()
    {
        return json_decode($this->attributes['renter_individual_rating_counts'], true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updateRentedItemsCount(int $numberOfItemsSold = 1): bool|int
    {
        return $this->increment('rented_items_count', $numberOfItemsSold);
    }

    public function updateListedItemsCount(int $numberOfItemsListed = 1): bool|int
    {
        return $this->increment('listed_items_count', $numberOfItemsListed);
    }

    public function updateBookingsPendingPickupCount(string $userType, int $count): void
    {
        $this->update([$userType . '_bookings_pending_pickup_count' => $count]);
    }

    public function updateBookingPendingAcceptanceCount(int $count): void
    {
        $this->update(['vendor_bookings_pending_acceptance_count' => $count]);
    }

//to Do => combine updateRenterAverageRating and updateVendorAverageRating into one method and give it a userType (vendor/renter)

    public function updateRenterAverageRating(float $averageRating, Collection $individualRatingCount, int $reviewsCount): bool
    {
        return $this->update([
            'renter_average_rating' => $averageRating,
            'renter_reviews_count' => $reviewsCount,
            'renter_individual_rating_counts' => [
                'oneRating' => $individualRatingCount[1] ?? 0,
                'twoRating' => $individualRatingCount[2] ?? 0,
                'threeRating' => $individualRatingCount[3] ?? 0,
                'fourRating' => $individualRatingCount[4] ?? 0,
                'fiveRating' => $individualRatingCount[5] ?? 0,
            ]
        ]);
    }

    public function updateVendorAverageRating(float $averageRating, Collection $individualRatingCount, int $reviewsCount): bool
    {
        return $this->update([
            'vendor_average_rating' => $averageRating,
            'vendor_reviews_count' => $reviewsCount,
            'vendor_individual_rating_counts' => [
                'oneRating' => $individualRatingCount[1] ?? 0,
                'twoRating' => $individualRatingCount[2] ?? 0,
                'threeRating' => $individualRatingCount[3] ?? 0,
                'fourRating' => $individualRatingCount[4] ?? 0,
                'fiveRating' => $individualRatingCount[5] ?? 0,
            ]
        ]);
    }

}
