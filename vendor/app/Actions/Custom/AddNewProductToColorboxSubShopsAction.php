<?php

namespace App\Actions\Custom;

use App\Jobs\CustomJobs\AddNewProductToColorboxSubShopsJob;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class AddNewProductToColorboxSubShopsAction
{
    public function handle(Product $product): JsonResponse
    {
        try {
            AddNewProductToColorboxSubShopsJob::dispatch($product)->onQueue('batch');

            return successfulJsonResponse([]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
