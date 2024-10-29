<?php

namespace App\Http\Actions\Transaction;

use App\Jobs\TransactionStatusCheckJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckTransactionStatusAction
{
    public function handle(Request $request): JsonResponse
    {
        logger($request['stan']);
        try {
            TransactionStatusCheckJob::dispatch($request['stan'])->onQueue('high');
            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
