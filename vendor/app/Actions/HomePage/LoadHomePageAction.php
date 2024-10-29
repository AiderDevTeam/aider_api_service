<?php

namespace App\Actions\HomePage;

use App\Custom\Status;
use App\Http\Resources\ClosetResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\VendorResource;
use App\Http\Services\SectionFilterService;
use App\Models\ProductAddress;
use App\Models\Product;
use App\Models\Section;
use App\Models\SubCategory;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class LoadHomePageAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### LOADING HOME PAGE ###');
        try {
            $sectionData = [];
            $homepageSections = Section::homepageSections()->simplePaginate($request->dataPerPage ?? 5);
            foreach ($homepageSections as $section) {
                logger($section);
                $sectionData[] = match (strtolower($section->type)) {
                    Section::PRODUCT => $section->loadProducts(),
                    Section::CATEGORY => $section->loadCategories(),
                    strtolower(Section::PRODUCT_CARD) => $section->loadProductCard(),
                    default => []
                };
            }

            return successfulJsonResponse(
                $sectionData
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }
}
