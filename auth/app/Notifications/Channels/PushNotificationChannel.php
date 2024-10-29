<?php

namespace App\Notifications\Channels;

use App\Http\Services\API\PushNotificationService;
use Illuminate\Notifications\Notification;

class PushNotificationChannel
{
    public function send($notifiable, Notification $notification): void
    {
        $data = $notification->toArray($notifiable);
        PushNotificationService::send($notifiable->push_notification_token, $data['title'], $data['body'], $data['data']);
    }
}
