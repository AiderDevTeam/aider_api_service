<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingProductExchangeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'external_id',
        'booking_product_id',
        'city',
        'origin_name',
        'country',
        'country_code',
        'longitude',
        'latitude',
        'time_of_exchange',
    ];

    protected $casts = ['time_of_exchange' => 'datetime'];

    public function bookingProduct(): BelongsTo
    {
        return $this->belongsTo(BookingProduct::class, 'booking_product_id');
    }

    public function getTimeOfExchangeAttribute(): string
    {
        return Carbon::parse($this->attributes['time_of_exchange'])->format('h:ia');
    }
}
