<?php

namespace App\Actions\Search;

use App\Custom\Status;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Http\Services\SearchService;
use App\Models\ProductAddress;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchProductAction
{
    public function handle(Request $request, SearchRequest $searchRequest): JsonResponse
    {
        try {
            logger('### PRODUCT SEARCH INITIATED ###');
            logger($searchRequest->validated());

            $productsFound = (new SearchService($searchRequest->validated('searchInput')))->productSearch();

            logger('### NUMBER OF PRODUCTS FOUND ###', [$productsFound->count()]);

            return $productsFound->count() < 1 ?
                errorJsonResponse(errors: ['no record found'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY) :
                paginatedSuccessfulJsonResponse(
                    ProductResource::collection($productsFound->with(['photos', 'prices', 'vendor.statistics', 'subCategoryItem', 'address', 'unavailableBookingDates'])->paginate($request->pageSize ?? 20))
                );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
