<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $notificationData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->notificationData;
        try {
//            logger('### BULK PUSH NOTIFICATION JOB DISPATCHED ###');
            User::query()->whereNot('push_notification_token', ['', null]
            )->chunk(5, function ($users) use ($data) {
                foreach ($users as $user) {
                    PushNotificationJob::dispatch($user, $data);
                }
            });
//            logger('### BULK PUSH NOTIFICATION JOB COMPLETED ###');
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
