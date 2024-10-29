<?php

namespace App\Models;

use App\Http\Resources\ProductAddressResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAddress extends Model
{
    use HasFactory, RealtimeModel;

    const APPROVED_REGIONS = [
        'Greater Accra',
        'Greater Accra Region',
        'Ashanti Region',
        'Ashanti'
    ];

    protected $fillable = [
        'id',
        'external_id',
        'product_id',
        'city',
        'origin_name',
        'country',
        'country_code',
        'longitude',
        'latitude',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function collection(): string
    {
        return 'product_addresses';
    }

    public function toRealtimeData(): ProductAddressResource
    {
        return new ProductAddressResource($this);
    }
}
