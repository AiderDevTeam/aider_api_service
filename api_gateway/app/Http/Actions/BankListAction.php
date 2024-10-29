<?php

namespace App\Http\Actions;

use App\Http\Services\PaystackService;
use Exception;
use Illuminate\Http\JsonResponse;

class BankListAction
{
    public function handle(): JsonResponse
    {
        try {
            if ($response = PaystackService::listBanks()) {
                return successfulJsonResponse($response['data']);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
