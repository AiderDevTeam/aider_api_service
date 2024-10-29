<?php

namespace App\Actions\Product;

use App\Custom\Status;
use App\Http\Requests\ProductDetailsUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\PriceStructure;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductDetailsUpdateAction
{
    public function handle(ProductDetailsUpdateRequest $request, Product $product): JsonResponse
    {
        try {
            logger('### UPDATING PRODUCT DETAILS ###');
            logger($requestPayload = $request->validated());

            DB::beginTransaction();

            $product->update(arrayKeyToSnakeCase($requestPayload));
            $this->updateAddress($product, $request);
            $this->updatePrice($product, $request);

            DB::commit();

            return successfulJsonResponse(
                data: new ProductResource($product->refresh()->load(['photos', 'prices', 'vendor', 'subCategoryItem', 'address'])),
                message: 'Product updated successfully'
            );

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    private function updateAddress(Product $product, ProductDetailsUpdateRequest $request): void
    {
        if ($request->filled('address')) {
            logger('### UPDATING PRODUCT ADDRESS ###');
            $product->address()->update(arrayKeyToSnakeCase($request->validated('address')));
        }
    }

    private function updatePrice(Product $product, ProductDetailsUpdateRequest $request): void
    {
        if ($request->has('prices')) {
            logger('### UPDATING PRODUCT PRICE ###');

            foreach ($request->validated('prices') as $price) {
                if (!is_null($price['priceStructureId'])) {
                    $priceStructure = PriceStructure::find($price['priceStructureId']);
                    $product->prices()->create([
                        'price' => $price['price'],
                        'start_day' => $priceStructure->start_day,
                        'end_day' => $priceStructure->end_day,
                    ]);
                    continue;
                }
                $product->prices()->find($price['productPriceId'])?->update(['price' => $price['price']]);
            }
        }
    }
}
