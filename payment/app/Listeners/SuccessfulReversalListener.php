<?php

namespace App\Listeners;

use App\Events\SuccessfulReversalEvent;
use App\Http\Resources\TransactionResource;
use App\Http\Services\NotificationService;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulReversalListener
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
    public function handle(SuccessfulReversalEvent $event): void
    {
        $payment = $event->payment;

        $transaction = $payment->transaction;
        $transaction->reversed();

        $this->successfulReversalNotification($payment);
    }

    public function successfulReversalNotification(Payment $payment): void
    {
        logger('### DISPATCHING SUCCESSFUL REVERSAL NOTIFICATION ###');

        $transactionDetails = new TransactionResource($payment->transaction);

        $pushMessage = "Hello $payment->collection_account_name,\nWe've successfully reversed the payment for your recent $payment->type purchase";
        (new NotificationService(['title' => 'Streamed Successful', 'body' => $pushMessage,
            'data' => json_encode($transactionDetails), 'userExternalId' => $payment->user->external_id]))->sendPush();
    }
}
