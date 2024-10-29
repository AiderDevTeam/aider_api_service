<?php

namespace App\Actions\Custom;

use App\Jobs\CustomJobs\SetUpColorboxSubShopsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetupColorboxSubShopsAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### SETTING UP COLORBOX SUB SHOPS ###');
        logger($request);
        try {
            SetUpColorboxSubShopsJob::dispatch($request->colorboxSubShops)->onQueue('high')->delay(now()->addSecond());

            return successfulJsonResponse([]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
