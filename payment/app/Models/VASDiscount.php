<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VASDiscount extends Model
{
    use HasFactory;

    protected $table = 'vas_discounts';

    protected $fillable = [
        'type',
        'discount'
    ];

    public static function findByType(string $type)
    {
        return self::where('type', '=', $type)?->first();
    }
}
