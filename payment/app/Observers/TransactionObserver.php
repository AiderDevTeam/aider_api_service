<?php

namespace App\Observers;

use App\Enum\Status;
use App\Events\ScoreUserPoyntAfterTransactionEvent;
use App\Events\SuccessfulTransactionNotificationEvent;
use App\Models\Transaction;


class TransactionObserver
{
    /**
     * Handle the Transaction "creating" event.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function creating(Transaction $transaction): void
    {
        $transaction->external_id = uniqid('TRAN');
        $transaction->status = Status::STARTED->value;
    }

    /**
     * Handle the Transaction "created" event.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function created(Transaction $transaction): void
    {
        $transaction->process();
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function updated(Transaction $transaction): void
    {
//        $transactionStatus = $transaction->getChanges()['status'] ?? null;
//
//        if ($transactionStatus && $transactionStatus === Status::COMPLETED->value) {
//            event(new ScoreUserPoyntAfterTransactionEvent($transaction));
//            event(new SuccessfulTransactionNotificationEvent($transaction));
//
////            manuallySyncModels([AdminMetric::query()->first()]);
//        }
    }
}
