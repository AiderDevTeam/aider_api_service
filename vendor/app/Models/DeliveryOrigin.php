<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrigin extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'origin_name',
        'city',
        'state',
        'country',
        'country_code',
        'latitude',
        'longitude',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
}
