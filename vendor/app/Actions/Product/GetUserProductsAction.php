<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserProductsAction
{
    public function handle(Request $request, User $user): JsonResponse
    {
        try {
            return paginatedSuccessfulJsonResponse(
                ProductResource::collection(
                    $user->products()->whereIn('status', [Status::ACTIVE, Status::PENDING])
                        ->with(['photos', 'prices', 'vendor.statistics', 'subCategoryItem', 'address', 'unavailableBookingDates'])
                        ->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20))
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
