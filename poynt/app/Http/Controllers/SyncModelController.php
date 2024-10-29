<?php

namespace App\Http\Controllers;

use App\Http\Actions\SyncModelAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncModelController extends Controller
{
    public function sync(Request $request, SyncModelAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
