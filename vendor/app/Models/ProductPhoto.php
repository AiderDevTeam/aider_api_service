<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPhoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'external_id',
        'product_id',
        'photo_url'
    ];

    protected $dates = ['deleted_at'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
