<?php

namespace App\Actions\WeightUnit;

use App\Http\Requests\WeightUnitRequest;
use App\Http\Resources\WeightUnitResource;
use App\Models\WeightUnit;
use Illuminate\Http\JsonResponse;

class StoreWeightUnitAction
{
    public function handle(WeightUnitRequest $request): JsonResponse
    {
        try {
            $weightUnit = WeightUnit::query()->create([
                'external_id' => uniqid(),
                ...$request->validated()
            ]);

            return successfulJsonResponse(
                data: new WeightUnitResource($weightUnit),
                message: 'Weight unit stored');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
