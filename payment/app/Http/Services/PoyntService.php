<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PoyntService
{
    public static function scorePoynt(
        string $type, string $action, array $actionResponsePayload, float $actionValue,
        string $userExternalId, int $debitPoynt = 0
    )
    {
        try {
            logger()->info('### DISPATCHING POYNT SCORE TO POYNT SERVICE ###');
            logger($url = 'poynt/api/user/update-poynt-balance/' . $userExternalId);
            $response = Http::withHeaders(jsonHttpHeaders())->post($url,
                [
                    'type' => $type,
                    'action' => $action,
                    'actionResponsePayload' => $actionResponsePayload,
                    'actionValue' => $actionValue,
                    'debitPoynt' => $debitPoynt
                ]);
            logger()->info('### RESPONSE FROM POYNT SERVICE ###');
            logger($response->json());
            return $response->successful() ?: $response->json()['message'] ?? null;

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
