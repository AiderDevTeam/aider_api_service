<?php

namespace App\Models;

use App\Traits\RunCustomQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Delivery extends Model
{
    use HasFactory, RunCustomQueries;

    const NEXT_DAY_DELIVERY = 'NEXT_DAY';
    const EXPRESS_DELIVERY = 'EXPRESS';
    const SAME_DAY_DELIVERY = 'SAME_DAY';

    const INTRACITY = 'intracity';
    const NATION_WIDE = 'nationwide';

    const DELIVERY_SERVICE_TYPES = [
        self::INTRACITY,
        self::NATION_WIDE
    ];

    const DELIVERY_OPTIONS = [
        self::EXPRESS_DELIVERY,
        self::NEXT_DAY_DELIVERY,
        self::SAME_DAY_DELIVERY
    ];

    protected $fillable = [
        'external_id',
        'status',
        'tracking_number',
        'service_webhook',
        'currency',
        'delivery_option',
        'is_pickup',
        'is_fulfillment_delivery',
        'amount_to_collect',
        'service_type',
        'is_prepaid_delivery',
        'pick_up_at'
    ];

    public function deliveryProcessor(): MorphTo
    {
        return $this->morphTo();
    }

    public function origin(): HasOne
    {
        return $this->hasOne(DeliveryOrigin::class);
    }

    public function destination(): HasOne
    {
        return $this->hasOne(DeliveryDestination::class);
    }

    public function recipient(): HasOne
    {
        return $this->hasOne(DeliveryRecipient::class);
    }

    public function sender(): HasOne
    {
        return $this->hasOne(DeliverySender::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    public function weGooDeliveries(): HasMany
    {
        return $this->hasMany(WegooDelivery::class);
    }

    public static function findWithTrackingNumber(string $trackingNumber): Model|Builder|null
    {
        return self::where('tracking_number', '=', $trackingNumber)?->first();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
