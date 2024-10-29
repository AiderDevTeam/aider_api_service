<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActionPoynt extends Model
{
    use HasFactory;

    const ACTION_TYPES = [
        'VALUE' => 'value',
        'NON_VALUE' => 'non value',
    ];

    protected $fillable = [
        'external_id',
        'action',
        'poynt',
        'type',
    ];

    public function userActionPoynts(): HasMany
    {
        return $this->hasMany(UserActionPoynt::class);
    }

    public function isValueBasedAction(): bool
    {
        return $this->type === self::ACTION_TYPES['VALUE'];
    }

    public function setActionAttribute($action): void
    {
        $this->attributes['action'] = strtolower($action);
    }

}
