<?php

namespace App\Actions\Category;

use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Exception;

class UpdateCategoryAction
{
    public function handle(Category $category, UpdateCategoryRequest $updateCategoryRequest)
    {
        try {
            logger('## Updating Category Details ##');

            $category->update(arrayKeyToSnakeCase($updateCategoryRequest->validated()));

            return successfulJsonResponse(data: [], statusCode: 204);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
