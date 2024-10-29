<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DeliveryProcessor extends Model
{
    use HasFactory;

    const PROCESSORS = [
        'WEGOO' => 'App\Models\WegooDelivery',
        'SHAQ_EXPRESS' => 'App\Models\ShaqExpressDelivery'
    ];

    protected $fillable = [
        'external_id',
        'name',
        'active',
        'express',
        'next_day',
    ];

    public function deliveries(): MorphMany
    {
        return $this->morphMany(Delivery::class, 'delivery_processor');
    }

    public static function activeProcessors()
    {
        return self::where('active', '=', true);
    }

    public static function nextDayDelivery()
    {
        return self::activeProcessors()->where('next_day', '=', true)->first();
    }

    public static function expressDelivery()
    {
        return self::activeProcessors()->where('express', '=', true)->first();
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

}
