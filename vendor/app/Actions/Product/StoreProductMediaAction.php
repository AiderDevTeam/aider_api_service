<?php

namespace App\Actions\Product;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Jobs\StoreProductMediaJob;
use App\Models\ProductMedia;
use App\Models\Product;
use Illuminate\Http\Response;
use App\Http\Requests\StoreProductMediaRequest;

class StoreProductMediaAction
{

    public function handle(Product $product, StoreProductMediaRequest $request)
    {
        logger("### ADD MEDIA FOR PRODUCT [$product->external_id] ###");

        try {
            StoreProductMediaJob::dispatch($product, $this->getLocalPaths($request))->onQueue('high');
            return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function getLocalPaths(StoreProductMediaRequest $request): array
    {
        try {
            $localPaths = [];
            foreach ($request->file('files') as $file) {
                $localPaths[] = $file->store('public/uploads');
            }
            return $localPaths;
        } catch (Exception $exception) {
            report($exception);
        }
        return [];
    }
}
