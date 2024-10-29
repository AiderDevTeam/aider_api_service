<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Resources\SubCategoryItemsResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategoryItem extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'external_id',
        'sub_category_id',
        'category_id',
        'name',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function collection(): string
    {
        return 'subcategory_items';
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): SubCategoryItemsResource
    {
        return new SubCategoryItemsResource($this);
    }

//    public function eligibleProducts(): Builder|HasMany
//    {
//        return $this->hasMany(Product::class)
//            ->whereHas('vendor.address', function ($query) {
//                $query->whereIn('state', ProductAddress::APPROVED_REGIONS);
//            })->where('products.quantity', '>', 0)
//            ->whereIn('products.status', [Status::ACTIVE, Status::PENDING]);
//    }
}
