<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait RunCustomQueries
{
    public static function findWithExternalId(string $externalId): Model|Builder|null
    {
        return self::query()->where('external_id', '=', $externalId)->first();
    }

    public static function findWithName(string $name): Model|Builder|null
    {
        return self::query()->where('name', '=', $name)->first();
    }

    public static function findWithUsername(string $username): Model|Builder|null
    {
        return self::query()->where('username', '=', $username)->first();
    }

    public static function findWithEmail(string $email): Model|Builder|null
    {
        return self::query()->where('email', '=', $email)->first();
    }
}
