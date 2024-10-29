<?php

namespace App\Http\Controllers;

use App\Actions\Category\ListCategoriesAction;
use App\Actions\Category\StoreCategoryAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(ListCategoriesAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function store(StoreCategoryAction $action, StoreCategoryRequest $request): JsonResponse
    {
        return $action->handle($request);
    }

    public function update(Category $category,UpdateCategoryAction $action, UpdateCategoryRequest $updateCategoryRequest): JsonResponse
    {
        return $action->handle($category, $updateCategoryRequest);
    }
}
