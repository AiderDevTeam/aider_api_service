<?php

namespace App\Http\Actions\System;

use App\Http\Requests\System\RegisterAdminMetricServiceRequest;
use App\Http\Resources\AdminMetricResource;
use App\Models\AdminMetric;
use Exception;
use Illuminate\Http\JsonResponse;

class RegisterAdminMetricServiceAction
{
    public function handle(RegisterAdminMetricServiceRequest $request): JsonResponse
    {
        try {
            return successfulJsonResponse(new AdminMetricResource(AdminMetric::query()->create($request->validated())));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
