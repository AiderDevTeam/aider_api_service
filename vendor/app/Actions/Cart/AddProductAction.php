<?php

namespace App\Actions\Cart;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddProductAction
{
    public function handle(Request $request, CartRequest $cartRequest): JsonResponse|null
    {
        try {
            $user = User::authUser($request->user);

            $product = Product::where('id', $cartRequest->validated('productId'))->first();

            if (($productInCart = $user->unCheckedOutCarts()->where('product_id', '=', $product->id)->first()) && $productInCart->exists()) {
                return !$productInCart->increase() ? errorJsonResponse(errors: ['Item already in cart'], statusCode: 422) : successfulJsonResponse(data: new CartResource($productInCart), message: 'Product quantity in cart increased');
            }

            $cart = Cart::query()->create([
                'user_id' => $user->id,
                'unit_price' => $product->unit_price,
                'discounted_amount' => $product->isDiscounted() ? $product->discounted_price : $product->unit_price,
                'unique_id' => setCartUniqueId($user),
                ...arrayKeyToSnakeCase($cartRequest->validated())
            ]);

            return successfulJsonResponse(
                data: new CartResource($cart),
                message: 'Product Added to Cart',
                statusCode: 201
            );

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }

}
