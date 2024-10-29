<?php

namespace App\Http\Services\API;

use Exception;
use Illuminate\Support\Facades\Http;

class EmailService
{
    public static function send(string $recipientEmail, string $message, string $subject)
    {
        try {
            logger()->info('### DISPATCHING EMAIL REQUEST TO API-GATEWAY ###');

            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('api-gateway/api/send-email', [
                    'recipientEmail' => $recipientEmail,
                    'message' => $message,
                    'subject' => $subject
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
