<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddCartAction;
use App\Actions\Cart\AddProductAction;
use App\Actions\Cart\DeleteCartAction;
use App\Actions\Cart\ListProductAction;
use App\Actions\Cart\UpdateCartAction;
use App\Actions\WebActions\WebAddProductAction;
use App\Http\Requests\CartRequest;
use App\Http\Requests\DeleteCartRequest;
use App\Http\Requests\ListProductsRequest;
use App\Http\Requests\QuantityRequest;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addProducts(Request $request, AddProductAction $action, CartRequest $cartRequest): JsonResponse
    {
        return $action->handle($request, $cartRequest);
    }
    public function webAddProducts(WebAddProductAction $action, CartRequest $cartRequest): JsonResponse
    {
        return $action->handle($cartRequest);
    }

    public function quantity(Cart $cart,UpdateCartAction $action, QuantityRequest $quantityRequest): JsonResponse
    {
        return $action->handle($cart, $quantityRequest);
    }

    public function list(ListProductAction $action, Request $request): JsonResponse
    {
        return $action->handle($request);
    }

    public function delete(DeleteCartAction $action, DeleteCartRequest $request):JsonResponse
    {
        return $action->handle($request);
    }
}
