<?php

namespace App\Jobs;

use App\Http\Services\IncentiveService;
use App\Http\Services\Payment\PayoutWalletService;
use App\Models\Incentive;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductListingIncentiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Product $product, private readonly Incentive $incentive)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### PRODUCT LISTING INCENTIVE JOB --- STARTED ###');
        try {

            (new IncentiveService($this->incentive))->sendCashIncentive();

        } catch (Exception $exception) {
            report($exception);
        }
        logger('### PRODUCT LISTING INCENTIVE JOB --- COMPLETED ###');
    }



}
