<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLikeLog extends Model
{
    use HasFactory;

    const STATES = [
        'LIKED' => 'liked',
        'UNLIKED' => 'unliked'
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'state'
    ];

    public static function log($userId, $productId, $state)
    {
        self::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'state' => $state
        ]);
    }
}
