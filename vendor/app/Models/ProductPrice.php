<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'product_id',
        'price',
        'start_day',
        'end_day',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
