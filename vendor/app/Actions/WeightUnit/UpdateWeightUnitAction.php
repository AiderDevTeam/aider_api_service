<?php

namespace App\Actions\WeightUnit;

use App\Http\Requests\UpdateWeightUnitRequest;
use App\Http\Resources\WeightUnitResource;
use App\Models\WeightUnit;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateWeightUnitAction
{
    public function handle(UpdateWeightUnitRequest $request, WeightUnit $weightUnit): JsonResponse
    {
        try {
            $weightUnit->update($request->validated());
            return successfulJsonResponse(
                data: new WeightUnitResource($weightUnit),
                message: 'Weight unit updated');
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
