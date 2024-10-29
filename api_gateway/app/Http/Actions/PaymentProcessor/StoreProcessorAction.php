<?php

namespace App\Http\Actions\PaymentProcessor;

use App\Http\Requests\StoreProcessorRequest;
use App\Models\Processor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreProcessorAction
{
    public function handle(StoreProcessorRequest $request): JsonResponse
    {
        try{
            if(Processor::query()->create(
                arrayKeyToSnakeCase($request->validated())
            )) return successfulJsonResponse(message:'Processor Stored', statusCode: Response::HTTP_CREATED);
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
