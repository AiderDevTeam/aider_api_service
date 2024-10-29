<?php

namespace App\Models;

use App\Traits\RunCustomQueries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Processor extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'active',
        'collect',
        'disburse',
        'direct_debit'
    ];

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'processor');
    }

    public function activate(): bool
    {
        return $this->update(['active' => true]);
    }

    public function isActive()
    {
        return $this->active;
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public static function activeProcessors(): Builder
    {
        return self::query()->where('active', '=', true);
    }

    public static function collectionProcessor(): Model|Builder|null
    {
        return self::activeProcessors()
            ->where('collect', '=', true)
            ->first();
    }

    public static function disbursementProcessor(): Model|Builder|null
    {
        return self::activeProcessors()
            ->where('disburse', '=', true)
            ->first();
    }

}
