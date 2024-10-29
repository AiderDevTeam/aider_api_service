<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRejection extends Model
{
    use HasFactory;

    const REASONS = [
        'INAPPROPRIATE_IMAGE' => 'inappropriate product image',
        'EXPLICIT_CONTENT' => 'explicit content',
        'INCORRECT_NAME' => 'incorrect product name',
        'INCORRECT_DESCRIPTION' => 'incorrect product description',
        'INCORRECT_COLOR_CHOICE' => 'incorrect color choice',
        'INCORRECT_CATEGORY' => 'incorrect category',
        'INCORRECT_CONDITION' => 'incorrect product condition',
        'INCORRECT_SIZE' => 'incorrect size'
    ];

    protected $fillable = [
        'product_id',
        'reason'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
