<?php

namespace App\Http\Actions;

use App\Http\Requests\StoreActionPoyntRequest;
use App\Http\Resources\ActionPoyntResource;
use App\Models\ActionPoynt;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class StoreActionPoyntAction
{
    public function handle(StoreActionPoyntRequest $request): JsonResponse
    {
        try {
            if ($actionPoint = ActionPoynt::query()->updateOrCreate(
                ['action' => $request->validated('action')],
                [
                    'poynt' => $request->validated('poynt'),
                    'type' => $request->validated('type')
                ]
            ))
                return successfulJsonResponse([new ActionPoyntResource($actionPoint)]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
