<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DeliveryProcessor extends Model
{
    use HasFactory;

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

    public static function activeProcessor()
    {
        return self::where('active', '=', true)->first();
    }}
