<?php

namespace App\Models;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SectionResource;
use App\Http\Services\SectionFilterService;
use App\Traits\RunCustomQueries;
use AppierSign\RealtimeModel\Traits\RealtimeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class Section extends Model
{
    use HasFactory, SoftDeletes, RunCustomQueries, RealtimeModel;

    const CLOSET = 'closet'; //out
    const PRODUCT = 'product';
    const PRODUCT_CARD = 'productCard';
    const SHOP = 'shop';
    const CATEGORY = 'category';
    const PRODUCT_BY_SHOP_TAG = 'productByShopTag';//out
    const PRODUCT_BY_SHOP_LOCATION = 'productByShopLocation';
    const TOP_CARD = 'topCard';

    const SECTION_TYPES = [
        self::PRODUCT,
        self::PRODUCT_CARD,
        self::CATEGORY,
    ];

    const SECTION_PAGE_TYPES = [
        'DISCOVERY_PAGE' => 'discovery',
        'HOMEPAGE' => 'homepage'
    ];

    protected $fillable = [
        'external_id',
        'name',
        'live',
        'position',
        'type',
        'filter',
        'filters',
        'page',
        'additional_data',
    ];

    public function getRouteKeyName(): string
    {
        return 'external_id';
    }

    public function setFilterAttribute(array $filter): void
    {
        $this->attributes['filter'] = json_encode($filter);
    }

    public function getFilterAttribute()
    {
        return json_decode($this->attributes['filter']);
    }

    public function setFiltersAttribute(array $filters): void
    {
        $this->attributes['filters'] = json_encode($filters);
    }

    public function getFiltersAttribute()
    {
        return json_decode($this->attributes['filters']);
    }

    public function setAdditionalDataAttribute($additionalData): void
    {
        if (!is_null($additionalData))
            $this->attributes['additional_data'] = json_encode($additionalData);
    }

    public function getAdditionalDataAttribute()
    {
        return json_decode($this->attributes['additional_data'] ?? '');
    }

    public static function liveSections()
    {
        return self::where('live', true);
    }

    public static function homepageSections()
    {
        return self::liveSections()->where('page', self::SECTION_PAGE_TYPES['HOMEPAGE'])->orderBy('position', 'ASC');
    }

    public static function discoveryPageSections()
    {
        return self::liveSections()->where('page', self::SECTION_PAGE_TYPES['DISCOVERY_PAGE'])->orderBy('position', 'ASC');
    }

    public function getSyncKey(): string
    {
        return 'external_id';
    }

    public function toRealtimeData(): SectionResource
    {
        return new SectionResource($this);
    }

    public function formattedData(): array
    {
        if (in_array($this->type, [self::PRODUCT_BY_SHOP_TAG, self::PRODUCT_BY_SHOP_LOCATION]))
            $this->type = self::PRODUCT;

        return [
            'externalId' => $this->external_id,
            'type' => $this->type,
            'position' => $this->position,
            'name' => $this->name,
            'data' => $this->data
        ];
    }

    public function loadProducts(): array
    {
        logger('### LOADING PRODUCTS SECTION DATA ###');

        $sectionFilterService = new SectionFilterService($this);

        $this->data = ProductResource::collection(
            $sectionFilterService->filterProducts()->with('photos', 'prices', 'vendor.statistics', 'address', 'unavailableBookingDates')
                ->inRandomOrder()->limit(4)->get()
        );

        return $this->formattedData();
    }

    public function loadProductCard(): array
    {
        logger('### LOADING PRODUCT CARD ###');

        $this->data = [
            'imageUrl' => $this->additional_data->image_url ?? ''
        ];
        return $this->formattedData();
    }

    public function loadCategories(): array
    {
        logger('### LOADING CATEGORIES SECTION DATA ###');

        $this->data = CategoryResource::collection(
            Category::where('status', true)->inRandomOrder()->limit(10)->get()
        );

        return $this->formattedData();
    }

    public function loadProductsByShopLocation(): array
    {
        logger('### LOADING PRODUCT BY SHOP LOCATION SECTION DATA ###');

        $this->data = ProductResource::collection(
            (new SectionFilterService($this))->filterProductsByShopLocation()
                ->with('photos', 'prices', 'vendor.statistics')->inRandomOrder()->limit(20)->get()
        );

        return $this->formattedData();
    }

}
