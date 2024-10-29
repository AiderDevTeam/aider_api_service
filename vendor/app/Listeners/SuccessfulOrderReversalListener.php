<?php

namespace App\Listeners;

use App\Events\SuccessfulOrderReversalEvent;
use App\Http\Services\NotificationService;
use App\Models\Order;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulOrderReversalListener
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
    public function handle(SuccessfulOrderReversalEvent $event): void
    {
        logger('### SUCCESSFUL ORDER REVERSAL EVENT TRIGGERED ###');
        try {
            $this->notifyOnSuccessfulReversal($event->order);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function notifyOnSuccessfulReversal(Order $order): void
    {
        $userExternalId = $order->user->external_id;
        $message = "Unfortunately, we are unable to process your order. We have refunded GHC $order->amount_paid to your wallet. #ShopOnPoynt";

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Successful Payout',
            'body' => $message,
            'data' => '',
            'notificationAction' => 'order'
        ]))->sendPush();
    }
}
