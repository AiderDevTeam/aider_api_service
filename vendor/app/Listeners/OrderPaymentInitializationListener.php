<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\OrderPaymentInitializationEvent;
use App\Events\SuccessfulOrderPlacementEvent;
use App\Http\Services\Api\DeliveryPaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderPaymentInitializationListener
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
    public function handle(OrderPaymentInitializationEvent $event): void
    {
        logger()->info('### ORDER PAYMENT INITIALIZATION EVENT TRIGGERED ###');

        if ($event->order->isPaymentOnDelivery()) {
            $event->order->update(['collection_status' => Status::SUCCESS]);
        } else {
            DeliveryPaymentService::create($event->request, $event->orderRequest,
                $event->order, $event->vendorId, $event->totalDiscountedAmount);
        }
    }
}
