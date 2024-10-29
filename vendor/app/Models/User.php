<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Custom\BookingStatus;
use App\Custom\Status;
use App\Http\Resources\ProductLikeResource;
use App\Http\Resources\UserResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use App\Http\Resources\VendorResource;
use App\Traits\RunCustomQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, RealtimeModel, RunCustomQueries;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'display_name',
        'commission',
        'details'
    ];

    const TYPES = [
        'RENTER' => 'renter',
        'VENDOR' => 'vendor'
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function reports(): MorphMany //reports made against user
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reportsMade(): HasMany //reports made by user
    {
        return $this->hasMany(Report::class, 'reporter_id', 'id');
    }

    public function hasReportedBooking(Booking $booking): bool
    {
        return $this->reportsMade()->where('booking_id', $booking->id)->exists();
    }

    public function ownsProduct(Product $product): bool
    {
        return $this->id === $product->user_id;
    }

    public function userBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function userPaidBookings(): HasMany
    {
        return $this->userBookings()
            ->where('booking_acceptance_status', BookingStatus::ACCEPTED)
            ->where('collection_status', BookingStatus::COLLECTION['SUCCESS']);
    }

    public function vendorBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vendor_id');
    }

    public function vendorPaidBookings(): HasMany
    {
        return $this->vendorBookings()
            ->where('booking_acceptance_status', BookingStatus::ACCEPTED)
            ->where('collection_status', BookingStatus::COLLECTION['SUCCESS']);
    }

    public function isVendor(): bool
    {
        return $this->products()->exists();
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(UserStatistics::class, 'user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function renterReviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function vendorReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id', 'id');
    }

    public function numberOfRenterReviews(): int
    {
        return $this->renterReviews()->count();
    }

    public function sumOfRenterRatings()
    {
        return $this->renterReviews()->sum('rating');
    }

    public function numberOfVendorReviews(): int
    {
        return $this->vendorReviews()->count();
    }

    public function sumOfVendorRatings()
    {
        return $this->vendorReviews()->sum('rating');
    }

    public function sentConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'user_id');
    }

    public function receivedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'vendor_id');
    }

    public function recordItemsRentedCount(int $numberOfItemsSold = 1)
    {
        return $this->statistics()->firstOrCreate()->updateRentedItemsCount($numberOfItemsSold);
    }

    public function recordItemsListedCount(int $numberOfItemsListed = 1)
    {
        return $this->statistics()->firstOrCreate()->updateListedItemsCount($numberOfItemsListed);
    }

    public function recordRenterAverageRating(): void
    {
        logger('### RECORDING RENTER AVERAGE RATING ###');

        $this->statistics()->firstOrCreate()
            ->updateRenterAverageRating(
                number_format($this->sumOfRenterRatings() / $this->numberOfRenterReviews(), 1),
                $this->renterIndividualRatingCounts(),
                $this->numberOfRenterReviews()
            );
    }

    public function recordVendorAverageRating(): void
    {
        logger('### RECORDING VENDOR AVERAGE RATING ###');

        $this->statistics()->firstOrCreate()
            ->updateVendorAverageRating(
                number_format($this->sumOfVendorRatings() / $this->numberOfVendorReviews(), 1),
                $this->vendorIndividualRatingCounts(),
                $this->numberOfVendorReviews()
            );
    }

    public function countOfVendorBookingsPendingPickup(): int
    {
        return $this->vendorPaidBookings()->where('vendor_pickup_status', Status::PENDING)->count();
    }

    public function countOfRenterBookingsPendingPickup(): int
    {
        return $this->userPaidBookings()->where('user_pickup_status', Status::PENDING)->count();
    }

    public function recordVendorBookingsPendingPickupCount(): void
    {
        $this->statistics()->firstOrCreate()
            ->updateBookingsPendingPickupCount(
                self::TYPES['VENDOR'],
                $this->countOfVendorBookingsPendingPickup()
            );
    }

    public function recordRenterBookingsPendingPickupCount(): void
    {
        $this->statistics()->firstOrCreate()
            ->updateBookingsPendingPickupCount(
                self::TYPES['RENTER'],
                $this->countOfRenterBookingsPendingPickup()
            );
    }

    private function vendorIndividualRatingCounts(): \Illuminate\Support\Collection
    {
        return $this->vendorReviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')->orderBy('rating')
            ->pluck('count', 'rating');
    }

    private function renterIndividualRatingCounts(): \Illuminate\Support\Collection
    {
        return $this->renterReviews()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')->orderBy('rating')
            ->pluck('count', 'rating');
    }

    private function countVendorBookingsPendingAcceptance(): int
    {
        return $this->vendorBookings()
            ->where('booking_acceptance_status', BookingStatus::PENDING)
            ->count();
    }

    public function recordVendorBookingPendingAcceptance(): void
    {
        $this->statistics()->firstOrCreate()->updateBookingPendingAcceptanceCount(
            $this->countVendorBookingsPendingAcceptance()
        );
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'subcategory_user');
    }

    public function setDetailsAttribute(?array $data): void
    {
        $this->attributes['details'] = json_encode($data);
    }

    public function getDetailsAttribute()
    {
        return json_decode($this->attributes['details'], true);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function unCheckedOutCarts(): HasMany
    {
        return $this->carts()->where('is_checked_out', '=', false);
    }

    public function likedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_likes')->withTimestamps()->withPivot('unliked');
    }

    public function likeProduct(Product $product)
    {
        return $this->likedProducts()->attach($product);
    }

    public function unlikeProduct(Product $product): int
    {
        return $this->likedProducts()->updateExistingPivot($product, ['unliked' => true]);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isGuest(): bool
    {
        return (boolean)$this->is_guest;
    }

    public static function authUser(array $authUser): Model|Builder
    {
        return self::query()->updateOrCreate([
            'external_id' => $authUser['externalId'],
        ], [
            'display_name' => $authUser['displayName'],
            'details' => $authUser
        ]);
    }

    public static function createGuestUser($guestUser): Model|Builder
    {
        if (self::query()->where('phone', '0' . substr($guestUser->recipient['phone'], -9))->exists()) {
            return self::query()->where('phone', '0' . substr($guestUser->recipient['phone'], -9))->first();
        } else {
            return self::query()->updateOrCreate([
                'external_id' => 'GUEST' . '0' . substr($guestUser->recipient['phone'], -9),
            ], ['full_name' => $guestUser->recipient['name'],
                'verification_status' => 'not started',
                'phone' => '0' . substr($guestUser->recipient['phone'], -9),
                'is_guest' => true]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @inheritDoc
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function userProductLikes(): array
    {
        $pivotData = $this->likedProducts->map(function ($product) {
            return $product->pivot;
        });
        return [
            'vendor' => VendorResource::collection($this->vendor()->get()),
            'productLikes' => ProductLikeResource::collection($pivotData)
        ];
    }

    public function toRealtimeData(): UserResource
    {
        return new UserResource($this->load('statistics'));
    }

    public function searchRecords(): HasMany
    {
        return $this->hasMany(UserSearchRecord::class, 'user_external_id', 'external_id');
    }

    public function vendorOrders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, Vendor::class)
            ->where('collection_status', Status::SUCCESS);
    }

}
