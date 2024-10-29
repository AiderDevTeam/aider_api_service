<?php

namespace App\Http\Services\API;

use Exception;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public static function send(string $pushNotificationToken, string $title, string $body, array $data): bool
    {
        try {
            logger()->info('### DISPATCHING PUSH NOTIFICATION REQUEST TO API-GATEWAY ###');
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('api-gateway/api/push-notifications', [
                    'pushNotificationToken' => $pushNotificationToken,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data
                ]);

            logger()->info('### PUSH NOTIFICATION RESPONSE FROM API-GATEWAY ###');
//            logger($request->json());
            return $request->successful();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
