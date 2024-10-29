<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GetProductsAction
{
    public function handle(): JsonResponse
    {
        try {
            logger('## FETCHING PRODUCTS ONTO HOME-PAGE ###');

            $categories = Category::whereIn('name', [
                'Fashion & Accessories', 'Phones, Tablets & Accessories', 'Electronics'
            ])
                ->orderBy('percentage', 'desc')
                ->get();

            $resultArray = [];

            foreach ($categories as $category) {

                $categoryData = [
                    'id' => $category->id,
                    'externalId' => $category->external_id,
                    'name' => $category->name,
                    'products' => ProductResource::collection($category->products()->whereHas('photos')->with('vendor', 'tags', 'weightUnit', 'reviews.reviewable')->inRandomOrder()->limit(20)->get())
                ];

                $resultArray[] = $categoryData;
            }
            return successfulJsonResponse($resultArray);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
