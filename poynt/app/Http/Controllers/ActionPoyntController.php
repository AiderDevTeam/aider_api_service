<?php

namespace App\Http\Controllers;

use App\Http\Actions\UpdateUserPoyntAction;
use App\Http\Actions\StoreActionPoyntAction;
use App\Http\Requests\UpdateUserPoyntRequest;
use App\Http\Requests\StoreActionPoyntRequest;
use App\Http\Resources\ActionPoyntResource;
use App\Models\ActionPoynt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActionPoyntController extends Controller
{
    public function index(): JsonResponse
    {
        return successfulJsonResponse(ActionPoyntResource::collection(ActionPoynt::query()->simplePaginate(10)));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreActionPoyntRequest $request
     * @param StoreActionPoyntAction $action
     * @return JsonResponse
     */
    public function store(StoreActionPoyntRequest $request, StoreActionPoyntAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
