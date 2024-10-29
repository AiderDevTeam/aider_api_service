<?php

namespace App\Jobs;

use App\Custom\Status;
use App\Models\Incentive;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class HandleIncentiveTransactionStatusUpdateJob implements ShouldQueue
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
        logger('### INCENTIVE TRANSACTION STATUS UPDATE JOB -- STARTED ###');
        try {
            Incentive::query()->where('amount', '>', 0)
                ->where('status', Status::PENDING)->chunk(10, function ($incentives) {

                    foreach ($incentives as $incentive) {
                        $response = Http::get("http://api-gateway/api/get-disbursement-transaction/$incentive->external_id");
                        logger($response);

                        if ($response->successful()) {
                            Http::post('http://vendor/webhooks/api-gateway-incentive-disbursement-response', $response->json()['data']);
                        }
                    }
                });
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### INCENTIVE TRANSACTION STATUS UPDATE JOB -- COMPLETED ###');
    }
}
