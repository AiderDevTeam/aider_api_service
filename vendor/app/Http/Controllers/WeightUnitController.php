<?php

namespace App\Http\Controllers;

use App\Actions\WeightUnit\StoreWeightUnitAction;
use App\Actions\WeightUnit\UpdateWeightUnitAction;
use App\Http\Requests\UpdateWeightUnitRequest;
use App\Http\Requests\WeightUnitRequest;
use App\Http\Resources\WeightUnitResource;
use App\Models\WeightUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeightUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return successfulJsonResponse(
            data: WeightUnitResource::collection(WeightUnit::query()->get()),
            message: 'Available weight units');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WeightUnitRequest $request, StoreWeightUnitAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(WeightUnit $weightUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWeightUnitRequest $request, WeightUnit $weightUnit, UpdateWeightUnitAction $action): JsonResponse
    {
        return $action->handle($request, $weightUnit);
    }
}
