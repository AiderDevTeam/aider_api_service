<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\FailedDeliveryEvent;
use App\Http\Resources\OrderResource;
use App\Http\Services\NotificationService;
use App\Jobs\OrderReversalJob;
use App\Models\Order;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class FailedDeliveryListener
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
    public function handle(FailedDeliveryEvent $event): void
    {
        $delivery = $event->delivery;
        $this->notifyUserOnFailedDelivery($delivery->order);
    }

    public function notifyUserOnFailedDelivery(Order $order): void
    {
        $user = $order->user;
        $message = "Hi!, we’re currently experiencing a high demand for delivery. We’ll let you know when we have a rider available.";

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'title' => 'Delivery Unavailable',
            'body' => $message,
            'data' => json_encode(new OrderResource($order->load('orderCarts'))),
            'notificationAction' => 'customer order status'
        ]))->sendPush();
    }
}
