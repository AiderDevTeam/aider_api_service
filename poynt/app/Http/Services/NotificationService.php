<?php

namespace App\Http\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    public function __construct(public array $notificationData)
    {
    }

    public function sendPush(): bool
    {
        $data = $this->notificationData;
        try {
            logger()->info('### DISPATCHING PUSH NOTIFICATION TO AUTH SERVICE ###');
            logger($url = 'auth/api/sys/send-push-notification/' . $data['userExternalId']);
            logger()->info('### DATA ### ', [$data]);

            $response = Http::withHeaders(jsonHttpHeaders())->post($url, [
                'title' => $data['title'],
                'body' => $data['body'],
                'data' => array(
                    'action' => $data['notificationAction'],
                    'resource' => $data['data']
                )
            ]);
            logger()->info('### PUSH NOTIFICATION TO AUTH SERVICE COMPLETED ###');
            logger($response->json());
            logger('### STATUS ###', [$response->status()]);
            return $response->successful();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
