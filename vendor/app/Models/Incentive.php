<?php

namespace App\Models;

use App\Custom\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Incentive extends Model
{
    use HasFactory;

    const TYPES = [
        'PRODUCT_LISTING' => 'product listing incentive'
    ];

    protected $fillable = [
        'external_id',
        'incentivable_type',
        'incentivable_id',
        'amount',
        'account_number',
        'sort_code',
        'description',
        'status',
    ];

    public function incentivable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function isSuccessful(): bool
    {
        return $this->status === Status::SUCCESS;
    }

    public function isProductListingIncentive(): bool
    {
        return $this->incentivable_type === Product::class;
    }
}
