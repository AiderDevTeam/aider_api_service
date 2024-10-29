<?php

namespace App\Listeners;

use App\Events\SaveIdVerificationLogEvent;
use App\Models\IdVerificationLog;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveIdVerificationLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaveIdVerificationLogEvent $event): void
    {
        $user = $event->user;
        $data = $event->data;
        try {
            $user->idVerificationLogs()->create([
                'external_id' => uniqid(),
                'status' => $data['status'],
                'response' => $data['response'],
            ]);
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
