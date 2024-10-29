<?php

namespace App\Models;

use App\Custom\Status;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
    use HasFactory, RealtimeModel;

    protected $fillable = [
        'external_id',
        'name',
        'status',
        'image_url'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'category_user');
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'category_vendor');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function subCategoryItems(): HasMany
    {
        return $this->hasMany(SubCategoryItem::class);
    }

    public function normalize(): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'name' => $this->name,
            'status' => (boolean)$this->status,
            'subCategories' => SubCategoryResource::collection($this->subcategories)
        ];
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, SubCategoryItem::class);
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): CategoryResource
    {
        return new CategoryResource($this->load('subcategories.subCategoryItems'));
    }

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

//    public function products(): HasManyThrough
//    {
//        return $this->has
//    }
}
