<?php

namespace App\Http\Controllers;

use App\Http\Actions\System\ListAdminMetricsAction;
use App\Http\Actions\System\RegisterAdminMetricServiceAction;
use App\Http\Requests\System\RegisterAdminMetricServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMetricController extends Controller
{
    public function index(ListAdminMetricsAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function store(RegisterAdminMetricServiceRequest $request, RegisterAdminMetricServiceAction $action)
    {

    }
}
