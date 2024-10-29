<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    const TYPES = [
        'RENTER' => 'renter',
        'PRODUCT' => 'product',
    ];

    protected $fillable = [
        'id',
        'reviewer_id',
        'reviewee_id',
        'external_id',
        'reviewable_id',
        'reviewable_type',
        'secondary_reviewable_id', //in the case of renter review, this contains id of the booked product
        'rating',
        'comment',
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function isBookingProductReview(): bool
    {
        return $this->reviewable_type === BookingProduct::class;
    }

    public function isRenterReview(): bool
    {
        return $this->reviewable_type === User::class;
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

}
