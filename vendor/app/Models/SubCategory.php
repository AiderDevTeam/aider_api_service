<?php

namespace App\Models;

use App\Custom\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'external_id'
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subcategory_user')
            ->whereIn('products.status', [Status::ACTIVE, Status::PENDING]);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, SubCategoryItem::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'size_subcategory');
    }

    public function eligibleProducts(): Builder|HasMany
    {
        return $this->hasMany(Product::class)
            ->whereHas('vendor.address', function ($query) {
                $query->whereIn('state', ProductAddress::APPROVED_REGIONS);
            })->where('products.quantity', '>', 0)
            ->whereIn('products.status', [Status::ACTIVE, Status::PENDING]);
    }

    public function subCategoryItems(): HasMany
    {
        return $this->hasMany(SubCategoryItem::class);
    }
}
