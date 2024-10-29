<?php

namespace App\Notifications\Channels;

use App\Http\Services\API\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        SmsService::send($notifiable->phone, $notification->toSms($notifiable));
    }
}
