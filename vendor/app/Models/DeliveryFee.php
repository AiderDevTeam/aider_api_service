<?php

namespace App\Models;

use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryFee extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'external_id',
        'processor',
        'delivery_option',
        'fee',
        'margin',
        'discounted_fee',
        'pay_on_delivery_fee',
        'pay_on_delivery_fee_margin',
    ];

    public static function getWegooDeliveryFee(string $deliveryOption)
    {
        return self::where('processor', '=', 'wegoo')->where('delivery_option', '=', $deliveryOption)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'processor' => $this->processor,
            'deliveryOption' => $this->delivery_option,
            'fee' => $this->fee,
            'margin' => $this->margin,
            'discountedFee' => (string)$this->discounted_fee,
            'isDiscounted' => $this->isDiscounted(),
            'payOnDeliveryFee' => (string)$this->pay_on_delivery_fee,
            'payOnDeliveryFeeMargin' => (string)$this->pay_on_delivery_fee_margin,
        ];
    }

    public function isDiscounted(): bool
    {
        return ($this->discounted_fee < $this->fee);
    }
}
