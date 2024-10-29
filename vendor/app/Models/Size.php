<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'external_id',
        'size_value'
    ];

    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(SubCategory::class, 'size_subcategory');
    }
}
