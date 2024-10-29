<?php

namespace App\Jobs;

use App\Custom\Status;
use App\Http\Services\Api\DeliveryService;
use App\Http\Services\NotificationService;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDeliveryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### PROCESS DELIVERY JOB RUNNING ###');
        $response = (new DeliveryService($this->order))->deliverWithWegoo();
        logger('### DELIVERY JOB DONE ###');
        logger($response);
    }

}
