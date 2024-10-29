<?php

namespace App\Http\Controllers;

use App\Actions\SyncIndividualModelsAction;
use App\Actions\SyncModelAction;
use App\Http\Requests\SyncIndividualModelsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncModelController extends Controller
{
    public function sync(Request $request, SyncModelAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function syncIndividualModels(SyncIndividualModelsRequest $request, SyncIndividualModelsAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
