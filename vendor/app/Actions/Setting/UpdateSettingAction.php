<?php

namespace App\Actions\Setting;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UpdateSettingAction
{
    public function handle(UpdateSettingRequest $request, Setting $setting): JsonResponse
    {
        logger("### UPDATING SETTING [$setting->external_id] ###");
        logger($request->validated());

        try {
            if ($setting->update(arrayKeyToSnakeCase($request->validated())))
                return successfulJsonResponse(data: [], statusCode: Response::HTTP_NO_CONTENT);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
