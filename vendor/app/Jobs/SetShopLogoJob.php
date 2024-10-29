<?php

namespace App\Jobs;

use App\Http\Services\Api\FileUploadService;
use App\Models\Vendor;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetShopLogoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Vendor $vendor, private readonly string $localFilePath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### SET UP SHOP LOGO JOB STARTED ###');
        try {
            $imageUrl = FileUploadService::uploadToImageService($this->localFilePath);
            $this->vendor->updateQuietly(['shop_logo_url' => $imageUrl]);
            $this->vendor->setShareLink();
        } catch (Exception $exception) {
            report($exception);
        }
        logger()->info('### SET UP SHOP LOGO JOB COMPLETED ###');
    }
}
