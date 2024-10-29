<?php

namespace App\Actions\Size;

use App\Http\Requests\SizeRequest;
use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;

class AddSizeAction
{
    public function handle(SizeRequest $request): JsonResponse
    {
        try{
            logger('### Adding Sizes ###');
            logger($request);
            $size = Size::query()->create([
                'name' => $request->name,
                'size_value' => $request->sizeValue
            ]);

            $subCategories = SubCategory::wherein('name', $request->subCategoryName)->get();

            foreach ($subCategories as $subCategory){
                $subCategory->sizes()->syncWithoutDetaching([$size->id]);
            }

            logger('### Adding Sizes Completed ###');

            return successfulJsonResponse(data: [], message: 'Size Added');
        } catch (\Exception $exception){
            report($exception);
        }

        return errorJsonResponse();
    }
}
