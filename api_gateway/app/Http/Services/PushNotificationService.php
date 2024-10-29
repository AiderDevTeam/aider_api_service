<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public static function send(string $pushNotificationToken, string $title, string $body, array $data = []): void
    {
        try {
            $requestBody = [
                'to' => $pushNotificationToken,
                'data' => $data,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default'
                ]
            ];

            logger()->info('### PUSH NOTIFICATION REQUEST ###');
            logger($requestBody);

            $request = Http::withHeaders([
                'Authorization' => env('PUSH_NOTIFICATION_API_KEY')
            ])->post(env('PUSH_NOTIFICATION_API_URL'), $requestBody);

            logger()->info('### PUSH NOTIFICATION RESPONSE ###');
            logger($request);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
