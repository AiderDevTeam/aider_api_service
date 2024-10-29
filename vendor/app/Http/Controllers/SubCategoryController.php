<?php

namespace App\Http\Controllers;

use App\Actions\SubCategory\ListSubCategoryAction;
use App\Actions\SubCategory\StoreSubCategoryAction;
use App\Actions\SubCategory\StoreUserSubCategoryAction;
use App\Actions\SubCategory\UpdateSubCategoryAction;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Http\Requests\UserSubCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(ListSubCategoryAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function store(StoreSubCategoryAction $action, SubCategoryRequest $request): JsonResponse
    {
        return $action->handle($request);
    }

    public function storeUserCategory(Request $request, StoreUserSubCategoryAction $action, UserSubCategoryRequest $userSubCategoryRequest): JsonResponse
    {
        return $action->handle($request, $userSubCategoryRequest);
    }

    public function update(UpdateSubCategoryAction $action, UpdateSubCategoryRequest $request): JsonResponse
    {
        return $action->handle($request);
    }

}
