<?php

namespace App\Actions\Cart;

use App\Http\Requests\CartRequest;
use App\Http\Requests\QuantityRequest;
use App\Models\Cart;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateCartAction
{
    public function handle(Cart $cart, QuantityRequest $cartRequest): JsonResponse
    {
        try {
            if($cartRequest['incremental']) {
                return !$cart->increase() ? errorJsonResponse(errors:['out of stock'], statusCode: 422) : successfulJsonResponse(data: [], message: 'Product quantity in cart increased', statusCode: 200);
            }

             return !$cart->decrease() ? errorJsonResponse(errors:['out of stock'], statusCode: 422) : successfulJsonResponse(data: [], message: 'Product quantity in cart decreased', statusCode: 200) ;

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
