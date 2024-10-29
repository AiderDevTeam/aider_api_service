<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Http\Services\PaymentService;
use App\Models\Payment;
use App\Models\VASPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReversalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        logger()->info('--- REVERSAL JOB STARTED ---');

        Payment::query()->where('collection_status', '=', Status::SUCCESS->value)
            ->where('disbursement_status', '=', Status::FAILED->value)
            ->whereIn('type', [VASPayment::AIRTIME_TOP_UP, VASPayment::DATA_BUNDLE_PURCHASE])
            ->whereNull ('reversal_status')
            ->chunk(5, function ($payments) {
                foreach($payments as $payment){

                    logger()->info(formatForLogging($payment->toArray()));

                   $this->disburse($payment);

                    $payment->setReversingStatus();
                }
            });

        logger()->info('--- REVERSAL JOB COMPLETED ---');
    }

    public function disburse(Payment $payment): void
    {
        logger()->info('--- REVERSAL DISBURSEMENT STARTED ---');

        $disbursement = new PaymentService([
            'accountNumber' => $payment->collection_account_number,
            'accountName' => $payment->collection_account_name,
            'amount' => toFloat(($payment->amount * 100)),
            'transactionId' => $payment->external_id,
            'description' => 'payment reversal',
            'callbackUrl' => env('REVERSAL_CALLBACK'),
            'rSwitch' =>  strtoupper($payment->collection_sort_code),
            'type' => Payment::REVERSAL
        ]);

        $disbursement->handle();
        $payment->incrementReversalTries();

        logger()->info('--- REVERSAL DISBURSEMENT COMPLETED ---');
    }
}
