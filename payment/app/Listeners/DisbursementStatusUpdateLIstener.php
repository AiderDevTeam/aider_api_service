<?php

namespace App\Listeners;

use App\Events\DisbursementStatusUpdateEvent;
use App\Jobs\CallbackJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DisbursementStatusUpdateLIstener
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
    public function handle(DisbursementStatusUpdateEvent $event): void
    {
        $payment = $event->payment;

        if ($payment->isBooking()) {
            logger('### SENDING BOOKING DISBURSEMENT STATUS UPDATE TO VENDOR SERVICE ###');
            logger($data = [
                'disbursementStatus' => $payment->disbursement_status,
                'bookingExternalId' => $payment->paymentable->booking_external_id,
                'paymentType' => 'disbursement'
            ]);

            CallbackJob::dispatch(
                $payment->paymentable->callback_url ?? env('DEFAULT_BOOKING_PAYMENT_CALLBACK_URL'),
                $data)->onQueue('high');
        }
    }
}
