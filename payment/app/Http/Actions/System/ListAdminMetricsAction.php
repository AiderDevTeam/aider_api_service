<?php

namespace App\Http\Actions\System;

use App\Http\Resources\AdminMetricResource;
use App\Models\AdminMetric;
use Exception;
use Illuminate\Http\JsonResponse;

class ListAdminMetricsAction
{
    public function handle(): JsonResponse
    {
        try {
            return successfulJsonResponse(AdminMetricResource::collection(AdminMetric::query()->get()));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
