<?php

namespace App\Observers;


use App\Enum\Status;
use App\Events\CollectionPaymentStatusUpdateEvent;
use App\Events\DisbursementStatusUpdateEvent;
use App\Events\FailedCollectionEvent;
use App\Events\FailedDisbursementEvent;
use App\Events\SuccessfulCollectionEvent;
use App\Events\SuccessfulDisbursementEvent;
use App\Events\SuccessfulReversalEvent;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class PaymentObserver
{
    /**
     * Handle the Payment "creating" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function creating(Payment $payment): void
    {
        $payment->external_id = uniqid('PAY');
    }

    public function created(Payment $payment): void
    {
    }


    /**
     * Handle the Payment "updated" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function updated(Payment $payment): void
    {
        logger()->info('### PAYMENT UPDATED ###');
        logger($payment->getChanges());

        if ($payment->isDirty('collection_status')) {
            event(new CollectionPaymentStatusUpdateEvent($payment));
        }

        if ($payment->isDirty('disbursement_status')) {
            event(new DisbursementStatusUpdateEvent($payment));
        }


    }

    /**
     * Handle the Payment "deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     *
     * @param Payment $payment
     * @return void
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
