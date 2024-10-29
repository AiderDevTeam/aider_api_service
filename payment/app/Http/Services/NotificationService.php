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
            logger()->info('### DATA ### ', [$data]);

            $response = Http::withHeaders(jsonHttpHeaders())->post('auth/api/sys/send-push-notification/' . $data['userExternalId'], [
                'title' => $data['title'],
                'body' => $data['body'],
                'data' => array(
                    'action' => 'transaction',
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

    public function sendSms(): bool
    {
        $data = $this->notificationData;
        try {
            logger()->info('### DISPATCHING SMS NOTIFICATION TO AUTH SERVICE ###');
            logger()->info('### DATA ### ', [$data]);

            $recipientData = !is_null($data['userExternalId'] ?? null) ? ['userExternalId' => $data['userExternalId']] : ['phone' => $data['phone']];

            $response = Http::withHeaders(jsonHttpHeaders())->post('auth/api/sys/send-sms-notification', [
                ...$recipientData,
                'message' => $data['message']
            ]);
            logger()->info('### SMS NOTIFICATION TO AUTH SERVICE COMPLETED ###');
            logger($response->json());
            logger('### STATUS ###', [$response->status()]);
            return $response->successful();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }
}
