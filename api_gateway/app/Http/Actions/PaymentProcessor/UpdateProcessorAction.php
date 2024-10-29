<?php

namespace App\Http\Actions\PaymentProcessor;

use App\Http\Requests\UpdateProcessorRequest;
use App\Models\Processor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdateProcessorAction
{
    public function handle(UpdateProcessorRequest $request, Processor $processor): JsonResponse
    {
        try{
            if($processor->update(arrayKeyToSnakeCase($request->validated())))
                return successfulJsonResponse(statusCode: Response::HTTP_NO_CONTENT);

        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
