<?php

namespace App\Jobs;

use App\Models\Order;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAmountPaidForPreviousOrdersJob implements ShouldQueue
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
        logger("### Generate Amount Paid For Previous Orders Job - STARTED ###");
        try{
            Order::query()->chunk(20, function($orders){
                foreach ($orders as $order){
                    $order->update(['amount_paid' => ($order->discounted_amount ?? $order->items_amount) + $order->delivery_amount]);
                }
            });
        }catch(Exception $exception){
            report($exception);
        }
        logger("### Generate Amount Paid For Previous Orders Job - COMPLETED ###");
    }
}
