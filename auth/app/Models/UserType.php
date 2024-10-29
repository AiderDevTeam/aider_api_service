<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserType extends Model
{
    use HasFactory;

    const UserTypes = [
        'RENTER' => 'renter',
        'VENDOR' => 'vendor',
    ];

    protected $fillable = [
        'id',
        'type'
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_usertype');
    }

    public static function getUserType(string $type): Model|Builder|null
    {
        return UserType::query()->where('type', '=', $type)?->first();
    }

}
