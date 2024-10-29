<?php

namespace App\Actions\SubCategory;

use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class ListSubCategoryAction
{
    public function handle(): JsonResponse
    {
        try {
            return successfulJsonResponse(SubCategory::query()->get());
        }catch (Exception $exception){
            report($exception);
        }

        return errorJsonResponse();
    }
}
