<?php

namespace App\Http\Controllers;

use App\Http\Actions\VendorService\ProductUpdateAction;
use App\Http\Requests\VendorService\ProductUpdateRequest;

class VendorServiceController extends Controller
{
    public function updateProduct(ProductUpdateRequest $request, ProductUpdateAction $action)
    {
        return $action->handle($request);
    }
}
