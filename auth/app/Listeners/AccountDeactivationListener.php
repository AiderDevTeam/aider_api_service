<?php

namespace App\Listeners;

use App\Events\AccountDeactivationEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AccountDeactivationListener
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
    public function handle(AccountDeactivationEvent $event): void
    {
        logger('### RECORDING ACCOUNT DEACTIVATION LOG ###');
        try {
            $event->user->accountDeactivationLogs()->create([
                'reason' => $event->accountDeactivationReason
            ]);
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
