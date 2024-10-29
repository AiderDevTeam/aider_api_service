<?php

namespace App\Actions\WebActions;

use App\Custom\Status;
use App\Http\Requests\WebGetProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WebGetProductsByVendorAction
{
    public function handle(WebGetProductsRequest $request): JsonResponse
    {
        try {
            logger("### LOADING PRODUCTS FOR @$request->shopTag SHOP ONTO SHOP-FRONT ###");

            if ($vendor = Vendor::where('shop_tag', $request->shopTag)->first()) {
                return successfulJsonResponse(
                    ProductResource::collection($vendor->getAvailableProducts()
                        ->whereIn('status', [Status::ACTIVE, Status::PENDING])
                        ->whereHas('photos')->orderBy('created_at', 'desc')
                        ->with(['vendor', 'subCategory'])->simplePaginate(250)) //$request->limit ?? 20
                );
            }
            logger('### SHOP FRONT NOT FOUND ###');
            return errorJsonResponse(message: 'Shop not found', statusCode: Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(errors: ['unable to load items. try again after sometime'], statusCode: 422);
    }

}
