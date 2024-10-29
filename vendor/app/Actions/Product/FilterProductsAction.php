<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FilterProductsAction
{
    public function handle(ProductFilterRequest $request): JsonResponse
    {
        logger('### FILTERING PRODUCTS ###');
        logger($payload = $request->validated());
        try {

            if (empty($payload))
                return errorJsonResponse(errors: ['select at least one filter option'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            $filteredProducts = Product::query()->whereIn('status', [Status::ACTIVE, Status::PENDING])
                ->whereHas('vendor', fn($vendor) => $vendor->whereJsonContains('details->status', Status::ACTIVE));

            $filters = [
                'subCategoryItemIds' => fn($query) => $query->whereIn('products.sub_category_item_id', $payload['subCategoryItemIds']),
                'priceRange' => fn($query) => $query->whereHas('prices', fn($prices) => $prices->whereBetween('product_prices.price', [$payload['priceRange']['min'], $payload['priceRange']['max']])),
                'location' => fn($query) => $query->whereHas('address', fn($address) => $address->whereIn('product_addresses.city', $payload['location']))
            ];

            foreach ($filters as $key => $filter) {
                if (isset($payload[$key])) {
                    $filteredProducts = $filter($filteredProducts);
                }
            }

            return paginatedSuccessfulJsonResponse(ProductResource::collection(
                $filteredProducts->with(['photos', 'prices', 'vendor', 'subCategoryItem', 'address', 'unavailableBookingDates'])
                    ->orderBy('created_at', 'DESC')->paginate($request->pageSize ?? 20)
            ));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
