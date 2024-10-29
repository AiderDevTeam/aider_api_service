<?php

namespace App\Actions;

use App\Models\Section;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoadDiscoveryPageAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### LOADING DISCOVERY PAGE ###');
        try {
            $sectionData = [];
            foreach (Section::discoveryPageSections()->simplePaginate($request->dataPerPage ?? 20) as $section) {
                logger($section);
                $sectionData[] = match (strtolower($section->type)) {
                    strtolower(Section::TOP_CARD) => $section->loadTopCards(),
                    Section::PRODUCT => $section->loadProducts(),
                    Section::SHOP => $section->loadVendors(),
                    Section::CLOSET => $section->loadClosets(),
                    Section::CATEGORY => $section->loadCategories(),
                    strtolower(Section::PRODUCT_BY_SHOP_TAG) => $section->loadProductsByShopTag(),
                    strtolower(Section::PRODUCT_BY_SHOP_LOCATION) => $section->loadProductsByShopLocation(),
                    default => []
                };
            }

            return successfulJsonResponse(data: $sectionData, message: 'Discovery Page');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }
}
