<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingProductDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_product_id',
        'booking_date'
    ];

    protected $dates = ['booking_date'];

    public function bookingProduct(): BelongsTo
    {
        return $this->belongsTo(BookingProduct::class);
    }
}
