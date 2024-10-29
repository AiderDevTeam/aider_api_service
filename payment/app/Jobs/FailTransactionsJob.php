<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FailTransactionsJob implements ShouldQueue
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
        logger()->info('---FAILING TRANSACTIONS JOB---');

        $transactions = Transaction::query()
            ->where('status', '=',Status::STARTED->value)
            ->where('created_at', '<', Carbon::now()->subDay())
            ->chunk(5, function ($transactions){
            foreach ($transactions as $transaction) {
                $transaction->update(['status' => 'failed']);
            }
        });


        logger()->info('--- FAILING TRANSACTIONS JOB COMPLETED ---');

    }
}
