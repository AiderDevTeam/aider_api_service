<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeightUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'unit',
        'abbreviation'
    ];

    public function setUnitAttribute(string $unit): void
    {
        $this->attributes['unit'] = strtolower($unit);
    }

    public function setAbbreviationAttribute(string $abbreviation): void
    {
        $this->attributes['abbreviation'] = strtolower($abbreviation);
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

}
