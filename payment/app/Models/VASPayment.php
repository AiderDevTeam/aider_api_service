<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VASPayment extends Model
{
    use HasFactory;

    const AIRTIME_TOP_UP = 'airtime';
    const DATA_BUNDLE_PURCHASE = 'data bundle';

    protected $fillable = [
        'external_id',
        'type',
        'description',
        'value'
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

