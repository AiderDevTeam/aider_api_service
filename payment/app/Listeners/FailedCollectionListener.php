<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class FailedCollectionListener
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
        $transaction = $payment->transaction;

        if ($payment->isDelivery()){
            $url = $transaction->callback_url;
            $data =
                [
                    'collectionStatus' => $payment->collection_status,
                    'deliveryExternalId' => $payment->paymentable->delivery_external_id,
                    'callbackUrl' => env('DELIVERY_CALLBACK'),
                    'responsePayload' => json_decode($transaction->response_payload)
                ];

            logger($data);

            logger("SENDING TO " . $url);

            Http::post(
                $url,
                $data
            );

        }

        $transaction->failed();
    }
}
