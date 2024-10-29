<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Models\Payment;
use App\Models\VASPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CustomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        custom job
    }

    public static function updateTransactionToReversed(): void
    {
        Payment::query()->where('collection_status', '=', Status::SUCCESS->value)
            ->where('disbursement_status', '=', Status::FAILED->value)
            ->where('reversal_status', '=', Status::REVERSING->value)
            ->chunk(5, function ($payments) {
                foreach ($payments as $payment) {
                    logger(formatForLogging($payment->toArray()));
                    $payment->updateQuietly([
                        'reversal_status' => Status::SUCCESS->value
                    ]);

                    $payment->transaction->updateQuietly([
                        'status' => Status::REVERSED->value
                    ]);
                }
            });
    }
}
