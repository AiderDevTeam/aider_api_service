<?php

namespace App\Http\Controllers;

use App\Actions\Report\ReportAction;
use App\Http\Requests\ReportRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends Controller
{
    public function report(Request $request, User $user, ReportAction $action, ReportRequest $reportRequest): JsonResponse
    {
        return $action->handle($request, $user, $reportRequest);
    }
}
