<?php

namespace App\Jobs\CustomJobs;

use App\Custom\Status;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetReferenceIdForColorboxProductsJob implements ShouldQueue
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
        logger()->info('### SETTING REFERENCE ID FOR COLORBOX PRODUCTS JOB --- STARTED ###');
        try {
            if ($colorboxShop = Vendor::findByTag(COLORBOX['MAIN_SHOP'])) {

                $colorboxShopProducts = $colorboxShop->getAvailableProducts()
                    ->whereHas('photos')
                    ->whereIn('status', [Status::ACTIVE, Status::PENDING])
                    ->whereNull('reference_id')->where('quantity', '>', 0)->get();

                $colorboxShopProducts->map(fn($product) => $product->update([
                    'reference_id' => uniqid('REF')
                ]));

                logger()->info('### SETTING REFERENCE ID FOR COLORBOX PRODUCTS JOB --- COMPLETED ###');
            }
            logger()->info('### SHOP NOT FOUND ###');
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
