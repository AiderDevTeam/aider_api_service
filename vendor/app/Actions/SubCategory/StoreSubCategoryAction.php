<?php

namespace App\Actions\SubCategory;

use App\Http\Requests\SubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Jobs\SubCategoryFileUpload;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class StoreSubCategoryAction
{
    public function handle(SubCategoryRequest $request): JsonResponse
    {
        try {

            $subCategory = SubCategory::query()->create(arrayKeyToSnakeCase($request->validated()));

            return successfulJsonResponse(
                data: new SubCategoryResource($subCategory),
                message: 'SubCategory created successfully',
                statusCode: 201
            );
        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
