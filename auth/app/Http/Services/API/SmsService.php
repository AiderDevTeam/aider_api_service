<?php

namespace App\Http\Services\API;

use Exception;
use Illuminate\Support\Facades\Http;

class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        try {
            logger()->info('### DISPATCHING SMS REQUEST TO API-GATEWAY ###');
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('api-gateway/api/send-sms', [
                    'to' => $phone,
                    'message' => $message,
                ]);

            logger()->info($request->json());
            logger()->info('### STATUS ' . $request->status() . ' ###');

            return $request->successful();

        } catch (Exception $exception) {
            report($exception);
        }

        return false;
    }
}
