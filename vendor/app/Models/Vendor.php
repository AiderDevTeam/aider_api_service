<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ReviewResource;
use App\Http\Services\GetAuthUserService;
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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

class Vendor extends Model implements ShareLinkInterface, ReviewInterface
{
    use HasFactory, RunCustomQueries;

    protected $fillable = [
        'user_id',
        'external_id',
        'shop_logo_url',
        'business_name',
        'shop_tag',
        'commission',
        'insurance',
        'share_link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(ProductAddress::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_vendor');
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->withTrashed();
    }

    public function eligibleProducts(): Builder|HasMany
    {
        return $this->hasMany(Product::class)
            ->whereHas('photos')
            ->whereIn('status', [Status::ACTIVE, Status::PENDING])
            ->where('quantity', '>', 0)->inRandomOrder();
    }

    public function getAvailableProducts(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function findByTag(string $shopTag)
    {
        return self::where('shop_tag', '=', $shopTag)->first();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(VendorAvailability::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(UserStatistics::class, 'vendor_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'vendor_id');
    }

//    public function reviews(): mixed
//    {
//        return Review::join('carts', 'reviews.reviewable_id', '=', 'carts.id')
//            ->where('carts.vendor_id', $this->id)
//            ->where('reviews.reviewable_type', Cart::class)
//            ->select('reviews.*');
//    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            Cart::class,
            'vendor_id', // Foreign key on carts table
            'reviewable_id', // Foreign key on reviews table
            'id', // Local key on Products table
            'id' // Local key on carts table
        );
    }

    public function numberOfReviews(): int
    {
        return $this->reviews()->count();
    }

    public function sumOfRatings(): int
    {
        return $this->reviews()->sum('rating');
    }

    public function recordAverageRating(): void
    {
        logger("### RECORDING VENDOR [$this->external_id] RATING ###");

        $this->statistics()->firstOrCreate()
            ->updateRating(
                number_format(($this->sumOfRatings() / $this->numberOfReviews()), 1),
                $this->individualRatingCounts()
            );
    }

    public function individualRatingCounts(): Collection
    {
        return Review::join('carts', 'reviews.reviewable_id', '=', 'carts.id')
            ->where('carts.vendor_id', $this->id)
            ->where('reviews.reviewable_type', Cart::class)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('count', 'rating');
    }

    public function recordItemsRentedCount(int $numberOfItemsSold = 1)
    {
        return $this->statistics()->firstOrCreate()->updateRentedItemsCount($numberOfItemsSold);
    }

    public function recordItemsListedCount(int $numberOfItemsListed = 1)
    {
        return $this->statistics()->firstOrCreate()->updateListedItemsCount($numberOfItemsListed);
    }

//    public function getSyncKey(): string
//    {
//        return 'external_id';
//    }

    public function getPayOnDeliveryAttribute(?bool $payOnDelivery): bool
    {
        return (boolean)$payOnDelivery ?? false;
    }

    public function getUserDetails(): array
    {
        return GetAuthUserService::getUser($this->user->external_id);
    }

//    public function toRealtimeData(): array
//    {
//        return [
//            'id' => $this->id,
//            'externalId' => $this->external_id,
//            'shopLogoUrl' => $this->shop_logo_url_b ?? $this->shop_logo_url,
//            'businessName' => $this->business_name,
//            'userExternalId' => $this->user->external_id,
//            'vendorName' => $this->user->full_name,
//            'username' => $this->user->other_details['username'] ?? '',
//            'verified' => $this->user->verification_status === 'approved',
//            'createdAt' => $this->created_at,
//            'shopTag' => $this->shop_tag,
//            'commission' => $this->commission,
//            'insurance' => $this->insurance,
//            'default' => (boolean)$this->default,
//            'address' => optional($this->address, fn() => new AddressResource($this->address)),
//            'categories' => CategoryResource::collection($this->categories),
//            'ts' => Carbon::parse($this->created_at)->timestamp,
//            'shareLink' => $this->share_link,
//            'joinedAt' => Carbon::parse($this->created_at)->format('jS F, Y'),
//            'type' => $this->type,
//            'official' => (boolean)$this->official,
//            'payOnDelivery' => $this->pay_on_delivery,
//            'paymentLink' => env('PAYMENT_LINK_URL') . $this->shop_tag,
//            'listedItemsCount' => $this->statistics?->listed_items_count,
//            'soldItemsCount' => $this->statistics?->sold_items_count,
//            'rating' => $this->statistics?->rating,
//            'numberOfReviews' => $this->numberOfReviews() ?? 0,
//            'reviews' => optional($this->reviews(), fn() => ReviewResource::collection($this->reviews->load('reviewable'))),
//            'individualRatingCounts' => $this->statistics?->individual_rating_counts
//        ];
//    }

    public function acceptsPaymentOnDelivery()
    {
        return $this->pay_on_delivery;
    }

    public function getShareLinkTitle(): ?string
    {
        return "@$this->shop_tag";
    }

    public function getShareLinkDescription(): ?string
    {
        return 'checkout my shop for cool items';
    }

    public function getShareLinkImage(): ?string
    {
        return $this->shop_logo_url;
    }

    public function setShareLink(): bool
    {
        logger('### SETTING VENDOR SHARE LINK ###');
        $link = GoogleDynamicLinksService::generateLink(
            "?externalId=$this->external_id&type=vendor",
            [
                'title' => $this->getShareLinkTitle(),
                'description' => $this->getShareLinkDescription(),
                'shareImage' => $this->getShareLinkImage() ?? env('DEFAULT_SHARE_IMAGE')
            ]);
        return $this->update(['share_link' => $link['link']]);
    }

    public function isPersonalShop(): bool
    {
        return $this->shop_tag === $this->user->other_details['username'];
    }

}
