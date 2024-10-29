<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SmsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $message)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     * @return string
     */
    public function via(object $notifiable): string
    {
        return SmsChannel::class;
    }

    public function toSms($notifiable): string
    {
        return $this->message;
    }

}
