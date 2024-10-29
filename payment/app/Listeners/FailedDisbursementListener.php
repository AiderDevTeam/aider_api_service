<?php

namespace App\Listeners;

use App\Events\FailedDisbursementEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FailedDisbursementListener
{
    /**
     * Handle the event.
     *
     * @param FailedDisbursementEvent $event
     * @return void
     */
    public function handle(FailedDisbursementEvent $event): void
    {
        logger()->info('--- FAILED DISBURSEMENT ---');
        logger()->info(formatForLogging($event->payment->toArray()));
    }
}
