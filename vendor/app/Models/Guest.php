<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'full_name',
        'phone',
        'vendor_id',
        'order_id'
    ];

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }
}
