<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait RunCustomQueries
{
    public static function findWithName(string $name): Builder|Model|null
    {
        return self::query()->where('name', '=', $name)->first();
    }

    public static function findWithExternalId(string $externalId): Builder|Model|null
    {
        return self::query()->where('external_id', '=', $externalId)->first();
    }
}
