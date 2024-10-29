<?php

namespace App\Actions\Custom;

use App\Jobs\CustomJobs\UpdateColorboxSubShopProductsJob;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateProductOnColorboxSubShopsAction
{
    public function handle(Product $product): JsonResponse
    {
        try {

            UpdateColorboxSubShopProductsJob::dispatch($product)->onQueue('batch');

            return successfulJsonResponse([]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
