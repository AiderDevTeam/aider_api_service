<?php

namespace App\Http\Controllers;

use App\Actions\Search\SearchAllAction;
use App\Actions\Search\SearchProductAction;
use App\Actions\Search\SearchProfileAction;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchProduct(Request $request, SearchRequest $searchRequest, SearchProductAction $action): JsonResponse
    {
        return $action->handle($request, $searchRequest);
    }

    public function searchProfile(Request $request, SearchRequest $searchRequest, SearchProfileAction $action): JsonResponse
    {
        return $action->handle($request, $searchRequest);
    }

    public function searchAll(Request $request, SearchRequest $searchRequest, SearchAllAction $action): JsonResponse
    {
        return $action->handle($request, $searchRequest);
    }
}
