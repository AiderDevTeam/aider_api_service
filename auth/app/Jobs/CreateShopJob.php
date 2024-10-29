<?php

namespace App\Jobs;

use App\Http\Services\Vendor\ShopService;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Services\API\FileUploadService;


class CreateShopJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $requestPayload, private readonly User $user, private ?string $shopLogoLocalPath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger('### CREATE SHOP JOB STARTED ###');

        try {
            $imageFileUrl = null;
            if (!is_null($this->shopLogoLocalPath)) {
                $imageFileUrl = FileUploadService::uploadToImageService($this->shopLogoLocalPath);
            }
            $this->requestPayload['shopLogo'] = $imageFileUrl;

            (new ShopService($this->requestPayload, $this->user))->createShop();

        } catch (Exception $exception) {
            report($exception);
        }
        logger('### CREATE SHOP JOB COMPLETED ###');
    }
}
