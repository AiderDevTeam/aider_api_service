<?php

namespace App\Actions\Product;

use App\Http\Requests\DeleteProductImageRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Response;

class DeleteProductImageAction
{
    public function handle(Product $product, DeleteProductImageRequest $request)
    {
        logger("## DELETING IMAGE OF PRODUCT [$product->external_id] ###");
        logger($request);

        try {
            $product->photos()->find($request->validated('photoId'))?->delete();

//            manuallySyncModels([$product]);

            return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
