<?php

namespace App\Actions\Report;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetReportsAction
{
    public function handle(Request $request): JsonResponse
    {
        $pageSize = $request->pageSize ?? 20;
        $resolved = $request->resolved;

        $query = Report::query()->with(['reportable', 'reporter']);

        if ($resolved !== null) {
            $isResolved = filter_var($resolved, FILTER_VALIDATE_BOOLEAN);
            if ($isResolved) {
                $query->whereNotNull('resolved_by');
            } else {
                $query->whereNull('resolved_by');
            }
        }

        $reports = $query->orderBy('created_at', 'DESC')->paginate($pageSize);

        return paginatedSuccessfulJsonResponse(ReportResource::collection($reports));

    }
}
