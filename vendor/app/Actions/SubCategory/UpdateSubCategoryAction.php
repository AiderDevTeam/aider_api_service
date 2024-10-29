<?php

namespace App\Actions\SubCategory;

use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;

class UpdateSubCategoryAction
{
    public function handle(UpdateSubCategoryRequest $request): JsonResponse
    {
        try {
            $subCategory = SubCategory::where('external_id', $request['externalId'])->first();
            $subCategory->update(arrayKeyToSnakeCase($request->validated()));

            manuallySyncModels([$subCategory->category]);
            return successfulJsonResponse(data: [], message: 'SubCategory successfully updated', statusCode: 204);
        } catch (\Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
