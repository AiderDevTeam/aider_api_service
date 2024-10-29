<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_external_id',
        'description',
        'callback_url'
    ];

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }
}
