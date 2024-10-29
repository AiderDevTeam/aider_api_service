<?php

namespace App\Actions\Search;

use App\Custom\Status;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VendorResource;
use App\Http\Services\SearchService;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchAllAction
{
    public function handle(Request $request, SearchRequest $searchRequest): JsonResponse
    {
        try {
            logger('### SEARCH INITIALIZED ###');
            logger($searchRequest->validated());

            $user = User::authUser($request->user);
            $searchService = new SearchService($searchRequest->validated('searchInput'));

            $profilesFound = $searchService->profileSearch();
            $productsFound = $searchService->productSearch();

            logger('## TOTAL RECORDS FOUND ###', [$totalRecords = $profilesFound->count() + $productsFound->count()]);
            logger('### NUMBER OF PROFILES FOUND ###', [$profileCount = $profilesFound->count()]);
            logger('### NUMBER OF PRODUCTS FOUND ###', [$productCount = $productsFound->count()]);

            $searchService->recordSearch($user, [
                'searchTerm' => $searchRequest->validated('searchInput'),
                'profileCount' => $profileCount,
                'productCount' => $productCount
            ]);

            if ($totalRecords > 0) {
                return successfulJsonResponse(data: [
                    'profileCount' => $profileCount,
                    'productCount' => $productCount,
                    'profiles' => UserResource::collection($profilesFound->limit(10)->get()),
                    'products' => ProductResource::collection($productsFound->with(['photos', 'prices', 'vendor.statistics', 'subCategoryItem', 'address', 'unavailableBookingDates'])->limit(20)->get())
                ]);
            }
            return errorJsonResponse(errors: ['no record found'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
