<?php

namespace App\Http\Controllers;

use App\Http\Actions\Payments\StoreCollectionAction;
use App\Http\Requests\CollectionRequest;
use Illuminate\Http\JsonResponse;

class CollectionController extends Controller
{
    public function __invoke(CollectionRequest $request, StoreCollectionAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
