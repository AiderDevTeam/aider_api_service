<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Resources\CartResource;
use App\Http\Resources\DeliveryDestinationResource;
use App\Http\Resources\DeliveryOriginResource;
use App\Http\Resources\DeliveryRecipientResource;
use App\Http\Resources\VendorResource;
use App\Http\Services\GetAuthUserService;
use App\Traits\RunCustomQueries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AppierSign\RealtimeModel\Traits\RealtimeModel;

class Order extends Model
{
    use HasFactory, RealtimeModel, RunCustomQueries;

    protected $fillable = [
        'external_id',
        'vendor_id',
        'user_id',
        'status',
        'collection_status',
        'delivery_amount',
        'discounted_amount',
        'items_amount',
        'destination',
        'description',
        'recipient_contact',
        'recipient_sort_code',
        'recipient_alternative_contact',
        'is_accepted',
        'disbursement_status',
        'disbursement_amount',
        'payout_commission',
        'order_number',
        'amount_paid',
        'reversal_status',
        'pay_on_delivery',
        'collection_response_payload',
    ];

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function setCollectionResponsePayloadAttribute(?array $data): void
    {
        $this->attributes['collection_response_payload'] = json_encode($data ?? []);
    }

    public function getCollectionResponsePayloadAttribute()
    {
        return json_decode($this->attributes['collection_response_payload'] ?? " ", true);
    }

    public function carts()
    {
        return Cart::where('order_id', '=', $this->external_id)->get();
    }

    public function orderCarts(): HasMany
    {
        return $this->hasMany(Cart::class, 'order_id', 'external_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function getPayOnDeliveryAttribute(?bool $payOnDelivery): bool
    {
        return $payOnDelivery ?? false;
    }

    public function toRealtimeData(): array
    {
        $user = GetAuthUserService::getUser($this->user->external_id);
        return [
            'externalId' => $this->external_id,
            'vendorId' => $this->vendor_id,
            'userExternalId' => $this->user->external_id,
            'vendorExternalId' => $this->vendor->external_id,
            'vendorUserExternalId' => $this->vendor->user->external_id,
            'status' => $this->status,
            'collectionStatus' => $this->collection_status,
            'reversalStatus' => $this->reversal_status,
            'deliveryAmount' => $this->delivery_amount,
            'discountedAmount' => $this->discounted_amount,
            'itemsAmount' => $this->discounted_amount,//$this->items_amount,
            'amountPaid' => $this->amount_paid,
            'description' => $this->description,
            'recipientContact' => $this->recipient_contact,
            'recipientSortCode' => $this->recipient_sort_code,
            'recipientAlternativeContact' => $this->recipient_alternative_contact,
            'isAccepted' => $this->is_accepted,
            'orderNumber' => $this->order_number,
            'ts' => Carbon::parse($this->created_at)->timestamp,
            'origin' => new DeliveryOriginResource($this->delivery->origin),
            'destination' => new DeliveryDestinationResource($this->delivery->destination),
            'recipient' => new DeliveryRecipientResource($this->delivery->recipient),
            'date' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'vendor' => new VendorResource($this->vendor),
            'user' => empty($user) ? null : $user,
            'carts' => CartResource::collection($this->carts()),
            'payOnDelivery' => $this->pay_on_delivery,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function decreaseProductsQuantity(): void
    {
        logger('### DECREASING PRODUCT QUANTITY AFTER CREATING ORDER ###');
        foreach (Cart::where('order_id', $this->external_id)->get() as $cart) {
            if ($cart->product->quantity > 0)
                $cart->product->decrement('quantity', $cart->quantity);
        }
    }

    public function increaseProductsQuantity(): void
    {
        logger('### INCREASING PRODUCT QUANTITY AFTER FAILED ORDER ###');
        foreach (Cart::where('order_id', $this->external_id)->get() as $cart) {
            $cart->product->increment('quantity', $cart->quantity);
        }
    }

    public function isReversing(): bool
    {
        return ($this->status === Status::DECLINED ||
                $this->status === Status::DELIVERY_STATUS['REJECTED'] ||
                $this->status === Status::DELIVERY_STATUS['CANCELED']) &&
            $this->collection_status === Status::SUCCESS &&
            $this->reversal_status === Status::PENDING;
    }

    public function isReversible(): bool
    {
        return ($this->status === Status::DECLINED ||
                $this->status === Status::DELIVERY_STATUS['REJECTED'] ||
                $this->status === Status::DELIVERY_STATUS['CANCELED']) &&
            $this->collection_status === Status::SUCCESS &&
            !$this->isPaymentOnDelivery() &&
            is_null($this->reversal_status);
    }

    public function isPayingOutVendor(): bool
    {
        return $this->status === Status::SUCCESS &&
            $this->collection_status === Status::SUCCESS &&
            $this->disbursement_status === Status::PENDING;
    }

    public function isPaymentOnDelivery(): bool
    {
        return $this->pay_on_delivery;
    }

    public function recordItemsRentedCount(): void
    {
        if ($this->status === Status::SUCCESS)
            $this->vendor->recordItemsRentedCount($this->orderCarts->count());
    }
}
