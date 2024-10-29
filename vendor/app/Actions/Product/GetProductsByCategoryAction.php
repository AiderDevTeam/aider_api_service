<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GetProductsByCategoryAction
{
    public function handle(Request $request, Category $category): JsonResponse
    {
        try {
            logger('### LOADING PRODUCTS FOR ' . strtoupper($category->name) . ' CATEGORY ###');

            return paginatedSuccessfulJsonResponse(
                ProductResource::collection(
                    $category->products()->whereIn('status', [Status::ACTIVE, Status::PENDING])
                        ->whereHas('vendor', fn($vendor) => $vendor->whereJsonContains('details->status', Status::ACTIVE))
                        ->with(['photos', 'prices', 'vendor', 'subCategoryItem', 'address', 'unavailableBookingDates'])
                        ->inRandomOrder()->paginate($request->pageSize ?? 20))
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['unable to load items. try again after sometime'], statusCode: 422);
    }
}
