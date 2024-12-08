<?php

namespace App\Actions\Product;

use App\Http\Requests\AddProductImageRequest;
use App\Jobs\ProductPhotoUpdateJob;
use App\Models\Product;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Cloudinary;

class AddProductImageAction
{
    public function handle(Product $product, AddProductImageRequest $request)
    {
        logger("### ADD IMAGES FOR PRODUCT [$product->external_id] ###");
        try {
            if (!is_array($request->file('photos')) && empty($request->file('photos')))
                return errorJsonResponse(errors: ['please select an image'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            $photoUrls = [];

            foreach ($request->file('photos') as $file) {                
                $photoUrl = Cloudinary::upload($file->getRealPath())->getSecurePath();
                if (!is_null($photoUrl)) {
                    $photoUrls[] = $photoUrl;
                }
            }

            if (!empty($photoUrls)) {
                foreach ($photoUrls as $photoUrl) {
                    $product->photos()->create(['photo_url' => $photoUrl]);
                }
                return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);
            }
        } catch (Exception $exception) {
            report($exception);
            logger()->error('Error during product photo upload: ' . $exception->getMessage());
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
