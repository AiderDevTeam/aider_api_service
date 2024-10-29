<?php

namespace App\Http\Services;

use App\Custom\Status;
use App\Http\Resources\ClosetResource;
use App\Models\Product;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SectionFilterService
{
    private $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function filterProducts(): ?Builder
    {
        return Product::query()->whereHas('photos')->where('quantity', '>', 0)
            ->whereIn('status', [Status::ACTIVE, Status::PENDING])
            ->whereHas('vendor', fn($vendor) => $vendor->whereJsonContains('details->status', Status::ACTIVE))
            ->where(function ($products) {
                foreach ($this->section->filter as $filterData) {
                    $filterType = strtolower($filterData->type);
                    $value = $filterData->value;
                    $relationship = strtolower($filterData->relationship);

                    if ($filterType === 'sub_category') {
                        $products->whereHas('subCategory', function ($subCategory) use ($value) {
                            $subCategory->whereIn('sub_categories.name', $value);
                        });
                        continue;
                    }

                    if ($filterType === 'sub_category_item') {
                        $products->whereHas('subCategoryItem', function ($subCategoryItem) use ($value) {
                            $subCategoryItem->whereIn('sub_category_items.name', $value);
                        });
                        continue;
                    }

                    if ($filterType === 'unit_price') {

                        $products->whereHas('prices', function ($prices) use ($value, $relationship) {
                            $priceRange = explode('-', $value[0] ?? "0-10");

                            $relationship === 'or' ?
                                $prices->orWhereBetween('product_prices.price', [(int)$priceRange[0] ?? 0, (int)$priceRange[1] ?? 0]) :
                                $prices->whereBetween('product_prices.price', [(int)$priceRange[0] ?? 0, (int)$priceRange[1] ?? 0]);

                        });
                        continue;
                    }

                    if ($filterType === 'day_range') {
                        $endDate = Carbon::today();
                        $startDate = $endDate->copy()->subDays($value[0] ?? 0);

                        $approvedProducts = $products->where('status', Status::ACTIVE);

                        $relationship === 'or' ?
                            $approvedProducts->orWhereBetween('created_at', [$startDate, $endDate]) :
                            $approvedProducts->whereBetween('created_at', [$startDate, $endDate]);
                        continue;
                    }

                    $relationship === 'or' ? $products->orWhereIn($filterType, $value) : $products->whereIn($filterType, $value);
                }
            });
    }

    public function filterProductsByShopLocation(): Builder
    {
        return Product::query()->whereHas('vendor.address', function ($query) {
            $query->whereIn($this->section->filter[0]->type, $this->section->filter[0]->value);
        })->whereHas('photos')
            ->where('quantity', '>', 0)
            ->whereIn('status', [Status::ACTIVE, Status::PENDING])
            ->whereHas('vendor', fn($vendor) => $vendor->whereJsonContains('details->status', Status::ACTIVE));
    }
}
