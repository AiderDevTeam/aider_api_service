<?php

namespace App\Http\Controllers;

use App\Actions\SubCategoryItems\StoreSubCategoryItemsAction;
use App\Http\Requests\StoreSubCategoryItemRequest;
use App\Http\Resources\SubCategoryItemsResource;
use App\Models\SubCategoryItem;
use Illuminate\Http\JsonResponse;

class SubCategoryItemsController extends Controller
{

    public function index(): JsonResponse
    {
        return successfulJsonResponse(SubCategoryItemsResource::collection(SubCategoryItem::query()->get()));
    }

    public function store(StoreSubCategoryItemsAction $action, StoreSubCategoryItemRequest $request): JsonResponse
    {
        return $action->handle($request);
    }


}
