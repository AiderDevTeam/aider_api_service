<?php

namespace App\Jobs;

use App\Http\Services\Payment\BookingPaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $requestPayload)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### PAYOUT JOB STARTED ###');
        BookingPaymentService::initializeDisbursement($this->requestPayload);
        logger('### PAYOUT JOB COMPLETED ###');
    }
}
