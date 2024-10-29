<?php

namespace App\Actions\Search;

use App\Custom\Status;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\VendorResource;
use App\Http\Services\SearchService;
use App\Models\ProductAddress;
use App\Models\Vendor;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SearchProfileAction
{
    public function handle(Request $request, SearchRequest $searchRequest): JsonResponse
    {
        try {
            logger('### VENDOR SEARCH INITIATED ###');
            logger($searchRequest->validated());

            $profilesFound = (new SearchService($searchRequest->validated('searchInput')))->profileSearch();
            logger('### NUMBER OF VENDORS FOUND ###', [$profilesFound->count()]);

            if ($profilesFound->count() > 0) {
                return paginatedSuccessfulJsonResponse(UserResource::collection($profilesFound->paginate($request->pageSize ?? 20)));
            }
            return errorJsonResponse(errors: ['no record found'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
