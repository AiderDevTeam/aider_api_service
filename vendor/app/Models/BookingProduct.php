<?php

namespace App\Models;

use App\Custom\BookingStatus;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BookingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'booking_id',
        'product_id',
        'product_amount',
        'product_quantity',
        'product_value',
        'start_date',
        'end_date',
        'booking_duration',
        'returned_early',
        'is_reviewed'
    ];

    protected $dates = ['start_date', 'end_date'];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function renter()
    {
        return $this->booking->user();
    }

    public function vendor()
    {
        return $this->booking->vendor();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bookingDates(): HasMany
    {
        return $this->hasMany(BookingProductDate::class);
    }

    public function exchangeSchedule(): HasOne
    {
        return $this->hasOne(BookingProductExchangeSchedule::class);
    }

    public function review(): MorphOne
    {
        return $this->morphOne(Review::class, 'reviewable');
    }

    public function renterReview(): HasOne
    {
        return $this->hasOne(Review::class, 'secondary_reviewable_id');
    }

    public function isReviewed(): bool
    {
        return $this->review()->exists();
    }

    public function reviewed(): void
    {
        $this->update(['is_reviewed' => true]);
    }

    public function isReviewable(): bool
    {
        return $this->booking?->status === BookingStatus::SUCCESS;
    }

    public function isOverdue(): bool
    {
        return Carbon::parse($this->end_date)->lt(Carbon::now());
    }

    //handles days overdue and days remaining
    public function daysSpan(): int
    {
        return Carbon::now()->diffInDays(Carbon::parse($this->end_date));
    }

}
