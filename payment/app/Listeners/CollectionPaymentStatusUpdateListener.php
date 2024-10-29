<?php

namespace App\Listeners;

use App\Events\CollectionPaymentStatusUpdateEvent;
use App\Jobs\CallbackJob;

class CollectionPaymentStatusUpdateListener
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
    public function handle(CollectionPaymentStatusUpdateEvent $event): void
    {
        $payment = $event->payment;

        if ($payment->isBooking()) {
            logger('### SENDING BOOKING COLLECTION PAYMENT STATUS UPDATE TO VENDOR SERVICE ###');

            CallbackJob::dispatch($payment->paymentable->callback_url ?? env('DEFAULT_BOOKING_PAYMENT_CALLBACK_URL'),
                [
                    'collectionStatus' => $payment->collection_status,
                    'bookingExternalId' => $payment->paymentable->booking_external_id,
                    'paymentType' => 'collection'
                ]
            )->onQueue('high');
        }
    }
}
