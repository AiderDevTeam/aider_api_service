<?php

namespace App\Listeners;

use App\Enum\Status;
use App\Events\SuccessfulCollectionEvent;
use App\Jobs\DisbursementJob;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class SuccessfulCollectionListener
{
    /**
     * Handle the event.
     *
     * @param SuccessfulCollectionEvent $event
     * @return void
     */
    public function handle(SuccessfulCollectionEvent $event): void
    {
        $payment = $event->payment;

        logger()->info('### SUCCESSFUL COLLECTION LISTENER ###');

        $transaction = $payment->transaction;
        $transaction->update(['status' => Status::COLLECTED->value]);

        if (!$payment->disbursement_stan) {

            if ($payment->isDelivery()) {
                $url = $payment->transaction->callback_url;
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

            } else DisbursementJob::dispatch($payment);
        }

    }
}
