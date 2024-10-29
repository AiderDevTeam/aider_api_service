<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAvailability extends Model
{
    use HasFactory;

    protected $fillable =[
        'external_id',
        'vendor_id',
        'day',
        'opening_time',
        'closing_time'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
