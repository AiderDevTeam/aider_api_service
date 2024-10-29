<?php

namespace App\Http\Controllers;

use App\Actions\Product\ProductLikeAction;
use App\Http\Requests\ProductLikeRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductLikeController extends Controller
{
    public function __invoke(Request $authRequest, Product $product, ProductLikeAction $action): JsonResponse
    {
        return $action->handle($authRequest, $product);
    }
}
