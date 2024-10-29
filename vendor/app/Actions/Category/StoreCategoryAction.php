<?php

namespace App\Actions\Category;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;

class StoreCategoryAction
{
    public function handle(StoreCategoryRequest $request)
    {
        try {

            $category = Category::query()->create(arrayKeyToSnakeCase($request->validated()));

            return successfulJsonResponse(data: new CategoryResource($category->refresh()));

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
