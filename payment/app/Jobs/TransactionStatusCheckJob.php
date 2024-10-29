<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Models\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class TransactionStatusCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $transactionReference)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### TRANSACTION STATUS CHECK JOB RUNNING ###');
        try {
            $response = Http::withHeaders(jsonHttpHeaders())->get('http://api-gateway/api/transaction/verify/' . $this->transactionReference);
            logger('### RESPONSE FROM API-GATEWAY ###');
            logger($response);

            if ($response->successful() && isset($response->json()['data'])) {
                $data = $response->json()['data'];
                $this->updateTransaction($this->transactionReference, $data);
            }

        } catch (Exception $exception) {
            report($exception);
        }

        logger('### TRANSACTION STATUS CHECK JOB COMPLETED ###');
    }

    private function updateTransaction(string $stan, array $data): void
    {
        try {
            if ($transaction = Transaction::where('stan', $stan)->first()) {

//                $status = !in_array($data['status'], [Status::PENDING->value, Status::SUCCESS->value]) ? Status::FAILED->value : $data['status'];

                if ($transaction->update(['status' => $data['status']])) {

                    logger('### TRANSACTION UPDATED ###');

                    if ($transaction->payment->update(['collection_status' => $data['status']]))
                        logger('### PAYMENT UPDATED ###');

                    return;
                }
            }

            logger('### TRANSACTION/PAYMENT UPDATE FAILED ###');

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
