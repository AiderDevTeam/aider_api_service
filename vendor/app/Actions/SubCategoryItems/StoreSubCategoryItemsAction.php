<?php

namespace App\Actions\SubCategoryItems;

use App\Http\Requests\StoreSubCategoryItemRequest;
use App\Http\Resources\SubCategoryItemsResource;
use App\Models\SubCategory;
use App\Models\SubCategoryItem;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class StoreSubCategoryItemsAction
{
    public function handle(StoreSubCategoryItemRequest $request): JsonResponse
    {
        try {
            $subCategoryItems = SubCategoryItem::query()->create([
                'external_id' => uniqid('SUCI'),
                'category_id' => SubCategory::query()->find($request->validated('subCategoryId'))->category->id,
                ...arrayKeyToSnakeCase($request->validated())
            ]);

            return successfulJsonResponse(data: new SubCategoryItemsResource($subCategoryItems));

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
