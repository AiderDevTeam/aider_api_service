<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SuccessfulDisbursementListener
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
    public function handle(object $event): void
    {
        $payment = $event->payment;
        $payment->disbursement_status_updated_at = now();
        $payment->saveQuietly();

        $transaction = $payment->transaction;
        $transaction->completed();
    }
}
