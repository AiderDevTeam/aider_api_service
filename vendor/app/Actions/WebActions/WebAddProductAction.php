<?php

namespace App\Actions\WebActions;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Guest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class WebAddProductAction
{
    public function handle(CartRequest $cartRequest): JsonResponse
    {
        try {
            logger('### CART REQUEST ###');
            logger($cartRequest);

            $user = User::createGuestUser($cartRequest);

             Guest::query()->create([
                'external_id' => $user->external_id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'vendor_id' => $cartRequest->vendorId
            ]);

            $product = Product::where('id', $cartRequest->validated('productId'))->first();

            if (($productInCart = $user->unCheckedOutCarts()->where('product_id', '=', $product->id)->first()) && $productInCart->exists()) {
                return successfulJsonResponse(
                    data: new CartResource($productInCart),
                    message: 'Product Added to Cart',
                    statusCode: 201
                );
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

        } catch (\Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }


}
