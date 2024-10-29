<?php

namespace App\Actions\SuperAdmin;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetProductsByStatusAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            logger('### LOADING PRODUCTS BY STATUS ###');
            logger($request->all());

            return paginatedSuccessfulJsonResponse(
                ProductResource::collection(
                    Product::query()->where('status', $request->status ?? Product::ACTIVE)
                        ->with(['photos', 'prices', 'vendor', 'subCategoryItem', 'address'])
                        ->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20)
                )
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
