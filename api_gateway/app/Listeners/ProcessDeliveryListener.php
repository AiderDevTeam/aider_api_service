<?php

namespace App\Listeners;

use App\Events\ProcessDeliveryEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessDeliveryListener
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
    public function handle(ProcessDeliveryEvent $event): void
    {
        $event->delivery->process();
    }
}
