<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    const PAY_ON_DELIVERY_REGIONS = [
        'greater accra region',
        'greater accra',
        'accra'
    ];

    protected $fillable = [
        'external_id',
        'user_id',
        'origin_name',
        'state',
        'city',
        'country',
        'county_code',
        'longitude',
        'latitude',
        'alternative_phone_number',
        'additional_information',
        'default',
        'first_name',
        'last_name',
        'phone'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function isEligibleForPayOnDelivery(): bool
    {
        return in_array(strtolower($this->state), self::PAY_ON_DELIVERY_REGIONS);
    }
}
