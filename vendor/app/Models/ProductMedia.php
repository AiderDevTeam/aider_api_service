<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'external_id',
        'product_id',
        'gifUrl',
        'mediaUrl',
        'mediaUrl_status'
    ];
    protected $dates = ['deleted_at'];
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
