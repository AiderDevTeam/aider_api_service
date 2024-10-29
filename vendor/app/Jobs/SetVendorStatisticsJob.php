<?php

namespace App\Jobs;

use App\Custom\Status;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetVendorStatisticsJob implements ShouldQueue
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
        logger('### SET VENDOR STATISTICS JOB --- STARTED --- ###');
        try {

            Vendor::query()->chunk(10, function ($vendors) {
                foreach ($vendors as $vendor) {

                    logger('### VENDOR ###', [$vendor]);

                    $numberOfItemsListedCount = $vendor->getAvailableProducts()->whereHas('photos')
                        ->whereIn('status', [Status::ACTIVE, Status::PENDING])->count();

                    $numberOfSoldItemsCount = $vendor->orders
                        ->where('status', Status::SUCCESS)->sum(
                            fn($order) => $order->orderCarts->count()
                        );

                    logger("### NUMBER OF ITEMS LISTED COUNT : $numberOfItemsListedCount ###");
                    logger("### NUMBER OF ITEMS SOLD COUNT : $numberOfSoldItemsCount ###");

                    $vendor->statistics()->updateorCreate([], [
                        'sold_items_count' => $numberOfSoldItemsCount,
                        'listed_items_count' => $numberOfItemsListedCount
                    ]);
                }
            });


        } catch (Exception $exception) {
            report($exception);
        }
        logger('### SET VENDOR STATISTICS JOB --- COMPLETED --- ###');
    }
}
