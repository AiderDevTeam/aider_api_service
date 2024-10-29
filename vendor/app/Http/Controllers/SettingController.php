<?php

namespace App\Http\Controllers;

use App\Actions\Setting\StoreSettingAction;
use App\Actions\Setting\UpdateSettingAction;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return successfulJsonResponse(SettingResource::collection(
            Setting::query()->paginate(20)
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSettingRequest $request, StoreSettingAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting, UpdateSettingAction $action): JsonResponse
    {
        return $action->handle($request, $setting);
    }

}
