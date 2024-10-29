<?php

namespace App\Notifications\Channels;

use App\Http\Services\API\EmailService;
use Illuminate\Notifications\Notification;

class EmailNotificationChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $data = $notification->toArray($notifiable);
        EmailService::send($notifiable->email, $data['message'], $data['subject']);
    }
}
