<?php

namespace App\Actions\Report;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ResolveReportAction
{
    public function handle(Request $authRequest, Report $report): JsonResponse
    {
        try {
            logger('### RESOLVING REPORT ###');

            $authUser = $authRequest->admin;

            $report->update([
                'resolved_by' => $authUser['externalId'],
                'resolved_on' => now(),
            ]);


            logger('### RESOLVED ###', [$report]);
            return successfulJsonResponse(data: [$report], message: 'Report has been resolved');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
