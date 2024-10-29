<?php

namespace App\Http\Controllers;

use App\Http\Actions\Payments\StoreDisbursementAction;
use App\Http\Requests\DisbursementRequest;
use Illuminate\Http\JsonResponse;

class DisbursementController extends Controller
{
    public function __invoke(DisbursementRequest $request, StoreDisbursementAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
