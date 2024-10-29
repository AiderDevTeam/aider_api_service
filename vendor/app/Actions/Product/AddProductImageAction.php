<?php

namespace App\Actions\Product;

use App\Http\Requests\AddProductImageRequest;
use App\Jobs\ProductPhotoUpdateJob;
use App\Models\Product;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AddProductImageAction
{
    public function handle(Product $product, AddProductImageRequest $request)
    {
        logger("### ADD IMAGES FOR PRODUCT [$product->external_id] ###");

        try {
            if (!is_array($request->file('photos')) && empty($request->file('photos')))
                return errorJsonResponse(errors: ['please select an image'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            ProductPhotoUpdateJob::dispatch($product, $this->getLocalPaths($request))->onQueue('high');

            return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function getLocalPaths(AddProductImageRequest $request): array
    {
        try {
            $localPaths = [];
            foreach ($request->file('photos') as $file) {
                $localPaths[] = $file->store('public/uploads');
            }
            return $localPaths;
        } catch (Exception $exception) {
            report($exception);
        }
        return [];
    }
}
