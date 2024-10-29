<?php

namespace App\Http\Controllers;

use App\Http\Actions\SyncModelToFireStoreAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncModelController extends Controller
{
    public function manuallySync(Request $request, SyncModelToFireStoreAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
