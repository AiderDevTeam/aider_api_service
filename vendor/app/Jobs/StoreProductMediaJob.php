<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Services\Api\FileUploadService;

class StoreProductMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Product $product, public array $media)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('### PRODUCT MEDIA UPLOAD JOB DISPATCHED ###');
        $mediaUrls = collect($this->uploadProductMedia())->map(function ($mediaUrl) {
            return ['mediaUrl' => $mediaUrl];
        });
        $this->product->media()->createMany($mediaUrls);

        $this->product->refresh()->setShareLink();

        logger()->info('### PRODUCT MEDIA UPLOAD COMPLETED ###');
    }

    public function uploadProductMedia(): array
    {
        $uploadedFiles = [];
        foreach ($this->media as $medium) {
            if (!is_null($medium))
                $uploadedFiles[] = (new FileUploadService($medium, true))->base64Upload()['url'];
        }
        logger($uploadedFiles);
        return $uploadedFiles;
    }
}
