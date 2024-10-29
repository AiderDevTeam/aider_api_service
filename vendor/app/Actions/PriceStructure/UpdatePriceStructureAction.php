<?php

namespace App\Actions\PriceStructure;

use App\Http\Requests\PriceStructureUpdateRequest;
use App\Models\PriceStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdatePriceStructureAction
{
    public function handle(PriceStructureUpdateRequest $request, PriceStructure $priceStructure): JsonResponse
    {
        logger("### UPDATING PRICE STRUCTURE [$priceStructure->external_id] ###");
        logger($request);
        try {
            $priceStructure->update(arrayKeyToSnakeCase($request->validated()));
            return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
