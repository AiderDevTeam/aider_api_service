<?php

namespace App\Jobs;

use App\Http\Services\PaymentService;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CollectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Payment $payment)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $collectionData = $this->getPaymentData();
        $collection = new PaymentService($collectionData);

        $collection->handle();
    }

    private function getPaymentData(): array
    {
        logger()->info('### PAYMENT COLLECTION STARTED ###');

        return [
            'transactionId' => getTransactionExternalIdByPayment($this->payment),
            'amount' => toFloat(($this->payment->amount * 100)),
            'rSwitch' => strtoupper($this->payment->collection_sort_code),
            'accountNumber' => $this->payment->collection_account_number,
            'description' => 'payment',
            'callbackUrl' => 'http://payment/webhooks/payment-collection-callback',

            'type' => Payment::COLLECTION,
        ];
    }


}
