<?php

namespace App\Actions\Category;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class ListCategoriesAction
{
    public function handle(): JsonResponse
    {
        try {
            return successfulJsonResponse(CategoryResource::collection(Category::query()->get()));
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
