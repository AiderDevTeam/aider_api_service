<?php

namespace App\Actions\PriceStructure;

use App\Http\Requests\PriceStructureStoreRequest;
use App\Http\Resources\PriceStructureResource;
use App\Models\PriceStructure;
use Exception;
use Illuminate\Http\JsonResponse;

class StorePriceStructureAction
{
    public function handle(PriceStructureStoreRequest $request): JsonResponse
    {
        logger('### STORING PRICE STRUCTURE ###');
        logger($request);
        try {
            $pricesStructure = PriceStructure::query()->create([
                'external_id' => uniqid('PS'),
                ...arrayKeyToSnakeCase($request->validated())
            ]);

            return successfulJsonResponse(new PriceStructureResource($pricesStructure));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
