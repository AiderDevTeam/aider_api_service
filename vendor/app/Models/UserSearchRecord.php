<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSearchRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_external_id',
        'search_term',
        'profiles_found',
        'products_found'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_external_id', 'external_id');
    }
}
