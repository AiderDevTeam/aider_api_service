<?php

namespace App\Actions\Setting;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StoreSettingAction
{
    public function handle(StoreSettingRequest $request): JsonResponse
    {
        logger('### CREATING NEW SETTING ###');
        logger($request->validated());
        try {
            $setting = Setting::create(arrayKeyToSnakeCase($request->validated()));
            return successfulJsonResponse(data: new SettingResource($setting));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
