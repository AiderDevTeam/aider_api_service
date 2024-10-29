<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Resources\ProductResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetProductsByVendorAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            $user = User::authUser($request->user);

            logger("### LOADING PRODUCTS FOR VENDOR $user->external_id ###");

            return paginatedSuccessfulJsonResponse(
                ProductResource::collection(
                    $user->products()->with(['photos', 'prices', 'vendor.statistics', 'subCategoryItem', 'address', 'unavailableBookingDates'])
                        ->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20))
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['unable to load items. try again after sometime'], statusCode: 422);
    }
}
