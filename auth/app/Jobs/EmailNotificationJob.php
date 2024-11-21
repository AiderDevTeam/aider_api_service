<?php

namespace App\Jobs;

use App\Http\Services\API\EmailService;
use App\Models\User;
use App\Notifications\EmailNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ?User $user, public ?string $recipientEmail, public string $message, public string $subject)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### DISPATCHING EMAIL NOTIFICATION JOB ###');
        try {
            logger()->info('### DISPATCHING EMAIL NOTIFICATION JOB ###');
            if ($this->user) {

                if (!$this->user->canReceiveEmailUpdates())
                    return;

                $this->user->notifications()->create([
                    'type' => 'email',
                    'data' => json_encode(['recipientEmail' => $this->user->email, 'subject' => $this->subject, 'message' => $this->message])
                ]);
                $this->user->notify(new EmailNotification($this->message, $this->subject));

            } else {

                if (!is_null($this->recipientEmail))
                    EmailService::send($this->recipientEmail, $this->message, $this->subject);
            }
            logger()->info('### EMAIL NOTIFICATION JOB COMPLETED ###');
        } catch (Exception $exception) {
            logger()->error('### EMAIL NOTIFICATION JOB ERROR ###', ['exception' => $exception]);
            report($exception);
        }
    }
}
