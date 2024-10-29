<?php

namespace App\Actions\Product;

use App\Http\Requests\ProductLikeRequest;
use App\Models\Product;
use App\Models\ProductLikeLog;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductLikeAction
{
    public function handle(Request $authRequest, Product $product): JsonResponse
    {
        try {
            $user = User::authUser($authRequest->user);
            $likedProduct = $user->likedProducts()->where('product_id', $product->id)->first();

            if ($likedProduct) {
                $likedProduct->pivot->unliked ?
                    $likedProduct->pivot->update(['unliked' => false]) : $user->unlikeProduct($product);
            } else
                $user->likeProduct($product);

            $likedState = $user->likedProducts()->where('product_id', $product->id)->first()->pivot->unliked;
            $state = $likedState ? ProductLikeLog::STATES['UNLIKED'] : ProductLikeLog::STATES['LIKED'];
            ProductLikeLog::log($user->id, $product->id, $state);

            manuallySyncModels([$user->refresh()]);
            manuallySyncModels([$product]);
            return successfulJsonResponse([], message: "product $state");

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
