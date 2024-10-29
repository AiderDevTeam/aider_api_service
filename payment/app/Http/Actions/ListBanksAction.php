<?php

namespace App\Http\Actions;

use App\Jobs\LoadBanksJob;
use Exception;
use Illuminate\Http\JsonResponse;

class ListBanksAction
{
    public function handle(): JsonResponse
    {
        logger('### LOADING BANK LIST ###');
        try {
            LoadBanksJob::dispatch()->onQueue('high');
            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
