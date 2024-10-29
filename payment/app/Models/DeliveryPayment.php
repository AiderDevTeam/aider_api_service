<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DeliveryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'description',
        'delivery_external_id',
    ];

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

