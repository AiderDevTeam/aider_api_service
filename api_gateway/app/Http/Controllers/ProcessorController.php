<?php

namespace App\Http\Controllers;

use App\Http\Actions\BankListAction;
use App\Http\Actions\PaymentProcessor\StoreProcessorAction;
use App\Http\Actions\PaymentProcessor\UpdateProcessorAction;
use App\Http\Requests\StoreProcessorRequest;
use App\Http\Requests\UpdateProcessorRequest;
use App\Http\Resources\ProcessorResource;
use App\Models\Processor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProcessorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            return successfulJsonResponse(
                ProcessorResource::collection(
                    Processor::query()->simplePaginate(10)
                ), 'Available Processors');
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProcessorRequest $request, StoreProcessorAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProcessorRequest $request, Processor $processor, UpdateProcessorAction $action): JsonResponse
    {
        return $action->handle($request, $processor);
    }

    public function listBanks(Request $request, BankListAction $action): JsonResponse
    {
        return $action->handle();
    }

}
