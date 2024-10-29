<?php

namespace App\Jobs;

use App\Http\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReversalDisbursementJob implements ShouldQueue
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
        logger()->info('--- REVERSAL DISBURSEMENT STARTED ---');

        $disbursement = new PaymentService([
            'accountNumber' => $this->payment->collection_account_number,
            'accountName' => $this->payment->collection_account_name,
            'amount' => toFloat(($this->payment->amount * 100)),
            'transactionId' => $this->payment->external_id,
            'description' => 'payment reversal',
            'callbackUrl' => env('REVERSAL_CALLBACK'),
            'rSwitch' =>  strtoupper($this->payment->collection_sort_code),
            'type' => Payment::REVERSAL
        ]);

        $disbursement->handle();
        $this->payment->incrementReversalTries();

        logger()->info('--- REVERSAL DISBURSEMENT COMPLETED ---');
    }
}
