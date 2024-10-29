<?php

namespace App\Actions;

use App\Http\Requests\SyncIndividualModelsRequest;
use App\Jobs\SyncIndividualModelsJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncIndividualModelsAction
{
    public function handle(SyncIndividualModelsRequest $request): JsonResponse
    {
        try {
            if ($modelName = formatModelName($request->modelName)) {
                SyncIndividualModelsJob::dispatch($request->validated('externalIds'), $modelName);
            }
            return successfulJsonResponse([]);
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
