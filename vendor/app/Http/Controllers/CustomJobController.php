<?php

namespace App\Http\Controllers;

use App\Actions\Custom\AddNewProductToColorboxSubShopsAction;
use App\Actions\Custom\UpdateProductOnColorboxSubShopsAction;
use App\Jobs\CustomJobs\UpdateColorboxSubShopProductsJob;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class   CustomJobController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        (new $request->name)->dispatch()->onQueue('batch');

        return successfulJsonResponse([]);
    }

//    public function testCronJob()
//    {
//        return (new UpdateColorboxSubShopProductsJob())->readCsV();
//    }
//
//    public function addNewProductToColorboxSubShops(Product $product, AddNewProductToColorboxSubShopsAction $action): JsonResponse
//    {
//        return $action->handle($product);
//    }
//
//    public function updateProductOnColorboxSubShops(Product $product, UpdateProductOnColorboxSubShopsAction $action): JsonResponse
//    {
//        return $action->handle($product);
//    }
}
