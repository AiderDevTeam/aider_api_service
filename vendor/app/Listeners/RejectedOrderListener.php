<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\RejectedOrderEvent;
use App\Http\Resources\OrderResource;
use App\Http\Services\GetAuthUserService;
use App\Http\Services\NotificationService;
use App\Jobs\OrderReversalJob;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class RejectedOrderListener
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
    public function handle(RejectedOrderEvent $event): void
    {
        $order = $event->order;
        $this->initiateReversal($order);
        $this->notifyUserOnRejectedOrder($order);
        $this->LogOrder($order);
    }

    public function notifyUserOnRejectedOrder(Order $order): void
    {
        $user = $order->user;
        $firstName = GetAuthUserService::getUser($user->external_id)['firstName'] ?? '';

        $message = "Hi $firstName, unfortunately @{$order->vendor->shop_tag} is unable to process your order. We have a range of other items available on the app you can check out.";

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $user->external_id,
            'title' => 'Product Unavailable',
            'body' => $message,
            'data' => json_encode(new OrderResource($order->load('orderCarts'))),
            'notificationAction' => 'customer order status'
        ]))->sendPush();
    }

    private function initiateReversal(Order $order): void
    {
        if ($order->isReversible()) {
            OrderReversalJob::dispatch($order)->onQueue('high');
        }
    }

    public function LogOrder(Order $order)
    {
        OrderLog::create([
            'status' => $order->status,
            'order_number' => $order->order_number,
        ]);
    }
}
