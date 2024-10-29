<?php

namespace App\Http\Actions;

use App\Jobs\SyncModelsToFirestoreJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SyncModelToFireStoreAction
{
    public function handle(Request $request): JsonResponse
    {
        try {
            if (!$request->has('modelName'))
                return errorJsonResponse(errors: ['model name'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if ($modelName = formatModelName($request->modelName)) {
                dispatch(new SyncModelsToFirestoreJob($modelName))->delay(now()->addSecond());
                return successfulJsonResponse(message: $request->modelName . ' syncing');
            }
            return errorJsonResponse(errors: ['Model not found'], statusCode: Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
