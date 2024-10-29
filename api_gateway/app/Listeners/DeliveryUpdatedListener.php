<?php

namespace App\Listeners;

use App\Events\DeliveryUpdatedEvent;
use App\Http\Resources\DeliveryResource;
use App\Jobs\CallbackJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeliveryUpdatedListener
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
    public function handle(DeliveryUpdatedEvent $event): void
    {
        logger()->info('### DISPATCHING CALLBACK JOB ###');
        $delivery = $event->delivery;
        CallbackJob::dispatch($delivery->service_webhook, deliveryToArray($delivery))->delay(now());
    }
}
