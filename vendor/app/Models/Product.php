<?php

namespace App\Models;

use App\Custom\BookingStatus;
use App\Custom\Status;
use App\Http\Services\GoogleDynamicLinksService;
use App\Interfaces\ReviewInterface;
use App\Interfaces\ShareLinkInterface;
use App\Traits\RunCustomQueries;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model implements ShareLinkInterface, ReviewInterface
{
    use HasFactory, SoftDeletes, RunCustomQueries;

    protected $fillable = [
        'external_id',
        'sub_category_item_id',
        'user_id',
        'name',
        'description',
        'value',
        'quantity',
        'status',
        'share_link',
        'approval_date',
        'rejection_date',
        'rating',
    ];

    protected $dates = ['deleted_at', 'approval_date', 'rejection_date'];

    const BRAND_NEW = 'brand new';
    const USED = 'used';
    const SEMI_USED = 'semi used';

    const ACTIVE = 'active';

    const PENDING = 'pending';
    const INACTIVE = 'inactive';

    const CONDITIONS = [
        self::BRAND_NEW,
        self::SEMI_USED,
        self::USED
    ];

    const STATUSES = [
        self::ACTIVE,
        self::INACTIVE,
        self::PENDING
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function setNameAttribute(string $name): void
    {
        $this->attributes['name'] = ucwords($name);
    }

    public function address(): HasOne
    {
        return $this->hasOne(ProductAddress::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function scopeWithTrashedPrices($query)
    {
        return $query->with(['photosWithTrashed']);
    }

    public function pricesWithTrashed()
    {
        return $this->hasMany(ProductPrice::class)->withTrashed();
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function hasSufficientQuantity(int $quantity): bool
    {
        return $this->quantity >= $quantity && $this->quantity > 0;
    }

    public function unavailableBookingDates(): HasManyThrough
    {
        return $this->hasManyThrough(BookingProductDate::class, BookingProduct::class)
            ->whereHas('bookingProduct.booking', function ($query) {
                $query->whereNotIn('status', [BookingStatus::SUCCESS, BookingStatus::FAILED]);
            });
    }

    public function reduceQuantityOnBooking(int $quantity): void
    {
        $this->decrement('quantity', $quantity);
    }

    public function increaseQuantity(int $quantity): void
    {
        $this->increment('quantity', $quantity);
    }

    public function photosWithTrashed(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->withTrashed();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subCategoryItem(): BelongsTo
    {
        return $this->belongsTo(SubCategoryItem::class);
    }

    public function subCategory(): HasOneThrough
    {
        return $this->hasOneThrough(
            SubCategory::class,
            SubCategoryItem::class,
            'id',
            'id',
            'sub_category_item_id',
            'sub_category_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function weightUnit(): BelongsTo
    {
        return $this->belongsTo(WeightUnit::class);
    }

    public function incentive(): MorphOne
    {
        return $this->morphOne(Incentive::class, 'incentivable');
    }

    public function hasReceivedListingIncentive(): bool
    {
        return $this->incentive()->exists() && $this->incentive?->status === Status::SUCCESS;
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'product_likes')->withPivot('unliked');
    }

    public function rejectionReasons(): HasMany
    {
        return $this->hasMany(ProductRejection::class);
    }

//    public function reports(): MorphMany
//    {
//        return $this->morphMany(Report::class, 'reportable');
//    }

    public function numberOfReviews(): int
    {
        return $this->reviews()->count();
    }

    public function sumOfRatings(): int
    {
        return $this->reviews()->sum('rating');
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            BookingProduct::class,
            'product_id', // Foreign key on bookingProducts table
            'reviewable_id', // Foreign key on reviews table
            'id', // Local key on Products table
            'id' // Local key on bookingProducts table
        )->where('reviewable_type', BookingProduct::class);
    }

    public function recordAverageRating(): void
    {
        logger("### RECORDING PRODUCT [$this->external_id] RATING ###");

        $this->update(
            ['rating' => number_format(($this->sumOfRatings() / $this->numberOfReviews()), 1)]
        );
    }

    public function numberOfLikes(): int
    {
        return $this->likedByUsers()->where('unliked', '=', false)->count();
    }

    public function getShareLinkTitle(): ?string
    {
        return $this->name;
    }

    public function getShareLinkDescription(): ?string
    {
        return $this->description;
    }

    public function getShareLinkImage(): ?string
    {
        return $this->photos->first()?->photo_url;
    }

    public function setShareLink(): bool
    {
        logger('### SETTING PRODUCT SHARE LINK ###');

        $link = GoogleDynamicLinksService::generateLink(
            "?externalId=$this->external_id&type=product",
            [
                'title' => $this->getShareLinkTitle(),
                'description' => $this->getShareLinkDescription(),
                'shareImage' => $this->getShareLinkImage() ?? env('DEFAULT_SHARE_IMAGE')
            ]);
        return $this->update(['share_link' => $link['link']]);
    }

    public function discountPercentage(): int
    {
        return (int)number_format(((($this->unit_price - $this->discounted_price) / $this->unit_price) * 100));
    }

    public function isDiscounted(): bool
    {
        return (
            !is_null($this->discounted_price) &&
            ($this->discounted_price > 0) &&
            ($this->discounted_price <= $this->unit_price) &&
            ($this->discountPercentage() > 0)
        );
    }

    public static function eligibleItems()
    {
        return self::whereHas('vendor.address', function ($query) {
            $query->whereIn('state', ProductAddress::APPROVED_REGIONS);
        })->whereHas('photos')->where('quantity', '>', 0)
            ->whereIn('status', [Status::ACTIVE, Status::PENDING]);
    }

    public function recordRejectionDate(): void
    {
        $this->updateQuietly(['rejection_date' => Carbon::now()]);
    }

    public function recordApprovalDate(): void
    {
        $this->updateQuietly(['approval_date' => Carbon::now()]);
    }

    public function isDelisted(): bool
    {
        return $this->status === self::INACTIVE;
    }

    public function recordItemListedCount(): void
    {
        $this->vendor->recordItemsListedCount();
    }

    public function recordDelistedItemCount(): void
    {
        logger()->info('### RECORDING DELISTED ITEM COUNT ###');
        if ($this->isDelisted() && ($this->vendor->statistics->listed_items_count > 0)) {

            $this->vendor->recordItemsListedCount(-1);
        }
    }
}
