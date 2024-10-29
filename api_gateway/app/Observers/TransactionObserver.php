<?php

namespace App\Observers;

use App\Events\TransactionCreatedEvent;
use App\Events\TransactionUpdatedEvent;
use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        logger()->info('### TRANSACTION CREATED ###');
        logger($transaction->load('processor')->toArray());

        event(new TransactionCreatedEvent($transaction->refresh()));
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        if($transaction->isDirty('status')){
            logger()->info('### TRANSACTION STATUS UPDATED ###');
            event(new TransactionUpdatedEvent($transaction));
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
