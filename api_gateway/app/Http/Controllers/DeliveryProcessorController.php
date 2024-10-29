<?php

namespace App\Http\Controllers;

use App\Http\Actions\Delivery\StoreDeliveryProcessorAction;
use App\Http\Actions\Delivery\UpdateDeliveryProcessorAction;
use App\Http\Requests\StoreDeliveryProcessorRequest;
use App\Http\Requests\UpdateDeliveryProcessorRequest;
use App\Http\Resources\DeliveryProcessorResource;
use App\Models\DeliveryProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryProcessorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return successfulJsonResponse(DeliveryProcessorResource::collection(DeliveryProcessor::query()->get()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryProcessorRequest $request, StoreDeliveryProcessorAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryProcessorRequest $request, DeliveryProcessor $deliveryProcessor, UpdateDeliveryProcessorAction $action): JsonResponse
    {
        return $action->handle($request, $deliveryProcessor);
    }

}
