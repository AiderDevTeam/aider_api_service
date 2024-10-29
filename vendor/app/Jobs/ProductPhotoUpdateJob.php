<?php

namespace App\Jobs;

use App\Http\Requests\AddProductImageRequest;
use App\Http\Services\Api\FileUploadService;
use App\Models\Product;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductPhotoUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Product $product, private array $filePaths)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### PRODUCT PHOTO UPDATE JOB DISPATCHED ###');

        try {
            foreach ($this->filePaths as $path) {

                $photoUrl = FileUploadService::uploadToImageService($path);

                if (!is_null($photoUrl))
                    $this->product->photos()->create(['photo_url' => $photoUrl]);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        logger()->info('### PRODUCT PHOTO UPDATE JOB COMPLETED ###');
    }
}
