<?php

namespace App\Jobs;

use App\Http\Services\API\SmsService;
use App\Models\User;
use App\Notifications\SmsNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?User $user, public ?string $phone, public string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            logger()->info('### DISPATCHING SMS NOTIFICATION JOB ###');
            if ($this->user) {

                if (!$this->user->canReceiveSMSNotifications())
                    return;

                $this->user->notifications()->create([
                    'type' => 'sms',
                    'data' => json_encode(['phone' => $this->user->phone, 'message' => $this->message])
                ]);
                $this->user->notify(new SmsNotification($this->message));

            } else {

                if (!is_null($this->phone))
                    SmsService::send($this->phone, $this->message);
            }
            logger()->info('### SMS NOTIFICATION JOB COMPLETED ###');
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
