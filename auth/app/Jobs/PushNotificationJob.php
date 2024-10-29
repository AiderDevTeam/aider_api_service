<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\PushNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public array $notificationData)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            logger()->info('### PUSH NOTIFICATION JOB DISPATCHED ###');
            logger($this->notificationData);

            if ($this->user->push_notification_token) {

                if (!$this->user->canReceivePushNotifications())
                    return;

                $this->user->notifications()->create([
                    'type' => 'push',
                    'data' => $this->notificationData
                ]);

                $this->user->notify(new PushNotification(
                    $this->notificationData['title'],
                    $this->notificationData['body'],
                    $this->notificationData['data']
                ));
            }
            logger('### PUSH NOTIFICATION JOB COMPLETED ###');
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
