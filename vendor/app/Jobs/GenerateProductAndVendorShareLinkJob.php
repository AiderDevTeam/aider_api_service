<?php

namespace App\Jobs;

use App\Http\Services\GoogleDynamicLinksService;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateProductAndVendorShareLinkJob implements ShouldQueue
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
        logger('### GENERATING SHARE LINKS FOR VENDORS AND PRODUCTS JOB STARTED ###');
        try {
            Vendor::query()->with('getAvailableProducts')->chunk(10, function ($vendors) {
                foreach ($vendors as $vendor) {

                    if ($shareLink = GoogleDynamicLinksService::generateLink(
                        "?externalId=$vendor->external_id&type=vendor",
                        $this->getSocialMetaTagInfo("@$vendor->shop_tag", 'checkout my shop for cool items', $vendor->shop_logo_url)
                    )) {
                        $vendor->update(['share_link' => $shareLink['link']]);
                    }

                    foreach ($vendor->getAvailableProducts as $product) {
                        if ($shareLink = GoogleDynamicLinksService::generateLink(
                            "?externalId=$product->external_id&type=product",
                            $this->getSocialMetaTagInfo($product->name, $product->description, $product->photos->first()?->photoUrl)
                        )) {
                            $product->update(['share_link' => $shareLink['link']]);
                        }
                    }
                }
            });
        } catch (Exception $exception) {
            report($exception);
        }
        logger('### GENERATING SHARE LINKS FOR VENDORS AND PRODUCTS JOB COMPLETED ###');

    }

    private function getSocialMetaTagInfo(string $title, string $description, string $shareImage = null): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'shareImage' => $shareImage ?? env('DEFAULT_SHARE_IMAGE')
        ];
    }
}
