<?php

namespace App\Models;

use App\Http\Resources\SettingResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Setting extends Model
{
    use HasFactory, RealtimeModel;

    const TYPES = [
        'SERVICE_FEE' => 'service_fee'
    ];

    protected $fillable = [
        'external_id',
        'type',
        'value'
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function getValueAttribute()
    {
        return match ($this->attributes['type']) {
            self::TYPES['SERVICE_FEE'] => (double)$this->attributes['value'],
            default => $this->attributes['value']
        };
    }

    public static function serviceFee()
    {
        return self::where('type', self::TYPES['SERVICE_FEE'])?->first()?->value;
    }

    public function toRealtimeData(): SettingResource
    {
        return new SettingResource($this);
    }

    public static function findByType(string $type)
    {
        return self::where('type', $type)->first();
    }

}
