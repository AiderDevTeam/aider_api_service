<?php

namespace App\Actions\HomePage;

use App\Custom\Status;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\VendorResource;
use App\Http\Services\SectionFilterService;
use App\Models\Category;
use App\Models\Section;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomePageSeeAllAction
{
    public function handle(Section $section): JsonResponse
    {
        try {
            logger('### LOADING ' . strtoupper($section->type) . ' SECTION SEE ALL PAGE ###');
            $sectionFilterService = new SectionFilterService($section);

            match (strtolower($section->type)) {
                Section::PRODUCT, strtolower(Section::PRODUCT_CARD) =>
                $section->data = ProductResource::collection(
                    $sectionFilterService->filterProducts()->with('photos', 'prices', 'vendor.statistics', 'address', 'unavailableBookingDates')
                        ->inRandomOrder()->simplePaginate(20)
                ),
                Section::CATEGORY => $section->data = CategoryResource::collection(
                    Category::where('status', true)->inRandomOrder()->simplePaginate(21)
                ),
                default => [],
            };

            return successfulJsonResponse($section->formattedData());

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }

}
