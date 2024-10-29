<?php

namespace App\Listeners;

use App\Events\TransactionUpdatedEvent;
use App\Jobs\CallbackJob;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TransactionUpdatedListener
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
    public function handle(TransactionUpdatedEvent $event): void
    {
        $transaction = $event->transaction;

        if ($transaction->isSuccessful()) {
            $transaction->response_code = '000';
            $transaction->response_message = 'processed successfully';
        } elseif ($transaction->status <> Transaction::PENDING) {
            $transaction->response_code = '444';
            $transaction->response_message = 'transaction failed';
        }

        $transaction->saveQuietly();

        CallbackJob::dispatch($transaction->callback_url, transactionToArray($transaction))->delay(now());
    }
}
