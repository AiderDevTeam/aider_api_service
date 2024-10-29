<?php

namespace App\Http\Controllers;

use App\Http\Actions\StoreDocumentAction;
use App\Http\Requests\FirestoreRequest;
use Illuminate\Http\JsonResponse;

class FirestoreController extends Controller
{
    public function __invoke(FirestoreRequest $request, StoreDocumentAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
