<?php

namespace App\Models;

use App\Http\Resources\PriceStructureResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceStructure extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'external_id',
        'name',
        'description',
        'start_day',
        'end_day',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function collection(): string
    {
        return 'price_structures';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): PriceStructureResource
    {
        return new PriceStructureResource($this);
    }

}
