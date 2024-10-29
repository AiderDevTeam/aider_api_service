<?php

namespace App\Http\Actions\VendorService;

use App\Http\Requests\VendorService\ProductUpdateRequest;
use App\Http\Services\Vendor\ProductService;
use Exception;

class ProductUpdateAction
{
    public function handle(ProductUpdateRequest $request)
    {
        try {
            $response = (new ProductService($request->validated()))->updateProduct();
            if ($response && $response->successful()) return successfulJsonResponse();
            return $response->json();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
