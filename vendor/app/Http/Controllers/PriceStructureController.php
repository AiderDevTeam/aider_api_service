<?php

namespace App\Http\Controllers;

use App\Actions\PriceStructure\UpdatePriceStructureAction;
use App\Actions\PriceStructure\StorePriceStructureAction;
use App\Http\Requests\PriceStructureStoreRequest;
use App\Http\Requests\PriceStructureUpdateRequest;
use App\Http\Resources\PriceStructureResource;
use App\Models\PriceStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return successfulJsonResponse(
            data: PriceStructureResource::collection(PriceStructure::query()->simplePaginate(20))
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PriceStructureStoreRequest $request, StorePriceStructureAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(PriceStructure $priceStructure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PriceStructure $priceStructure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PriceStructureUpdateRequest $request, PriceStructure $priceStructure, UpdatePriceStructureAction $action): JsonResponse
    {
        return $action->handle($request, $priceStructure);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PriceStructure $priceStructure)
    {
        //
    }
}
