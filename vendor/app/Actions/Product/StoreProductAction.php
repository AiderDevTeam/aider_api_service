<?php

namespace App\Actions\Product;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Jobs\ProductPhotosUploadJob;
use App\Models\PriceStructure;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;
use Cloudinary;

class StoreProductAction
{
    public function handle(Request $request, StoreProductRequest $productRequest): JsonResponse
    {
        try {
            logger('### UPLOADING PRODUCT ###');
            logger($productRequest->except('photos'));

            $data = $productRequest->validated();

            if (!$productRequest->has('photos') || is_null($productRequest->file('photos'))) {
                return errorJsonResponse(errors: ['photos field is required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $user = User::authUser($request->user);

            $product = $user->products()->create(arrayKeyToSnakeCase($data));

            $this->storePrices($product, $data['prices']);

            $this->storeAddress($product, $productRequest, $request->user);
            
            //upload product images and set product share link
            $this->storeProductImages($product, $productRequest);
            // ProductPhotosUploadJob::dispatch($product,$productRequest)->onQueue('high');

            DB::commit();

            return successfulJsonResponse(new ProductResource($product->refresh()->load(['photos', 'prices', 'vendor', 'subCategoryItem', 'address'])));

        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }

    private function storeAddress(Product $product, StoreProductRequest $request, array $user): void
    {
        $addressData = $request->has('address') ? json_decode($request->validated('address'), true) : ($user['addresses'][0] ?? []);

        if (!empty($addressData)) {
            $product->address()->create([
                'product_id' => $product->id,
                'city' => $addressData['city'],
                'origin_name' => $addressData['originName'],
                'country' => $addressData['country'],
                'country_code' => $addressData['countryCode'],
                'longitude' => $addressData['longitude'],
                'latitude' => $addressData['latitude'],
            ]);
        }
    }

    private function storePrices(Product $product, array $prices): void
    {
        try {
            $prices = collect($prices)->map(function ($priceSet) {
                $price = json_decode($priceSet, true);
                $priceStructure = PriceStructure::find($price['priceStructureId']);
                return [
                    'price' => $price['price'],
                    'start_day' => $priceStructure->start_day,
                    'end_day' => $priceStructure->end_day,
                ];
            })->toArray();
            $product->prices()->createMany($prices);
        } catch (Exception $exception) {
            report($exception);
            logger('### ERROR STORING PRODUCT PRICES ###');
        }

    }

    private function getLocalPaths(StoreProductRequest $request): array
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

    private function storeProductImages(Product $product, StoreProductRequest $request): void
    {
        logger()->info('### PRODUCT PHOTOS UPLOAD STARTED ###');
        try {
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
            }
        } catch (Exception $exception) {
            report($exception);
            logger()->error('Error during product photo upload: ' . $exception->getMessage());
        }
        logger()->info('### PRODUCT PHOTOS UPLOAD COMPLETED ###');
    }
}
