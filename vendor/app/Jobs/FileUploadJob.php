<?php

namespace App\Jobs;

use App\Http\Services\GoogleDynamicLinksService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class FileUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $vendorRequest;
    private $vendor;

    /**
     * Create a new job instance.
     */
    public function __construct($vendorRequest, $vendor)
    {
        //
        $this->vendorRequest = $vendorRequest;
        $this->vendor = $vendor;

        //dd($this->runProc());

    }

    /**
     * Execute the job.
     */
    public function handle(): bool
    {
        $this->runProc();
        return false;
    }

    private function runProc(): void
    {
        try {
            logger('### SHOP LOGO UPLOAD JOB RUNNING ###');

            logger()->info('http://api-gateway/api/file-upload');
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('http://api-gateway/api/file-upload', [
                    'image' => true,
                    'file' => $this->vendorRequest['shopLogo'],
                ]);
            logger([
                'status' => $request->status(),
                'code' => $request->successful(),
                'response' => $request
            ]);
            if ($request->successful()) $this->vendor->update(['shop_logo_url' => $request->json()['data']['file']['url']]);

            $this->vendor->setShareLink();

        } catch (Exception $exception) {
            logger([
                'status' => 'Something went wrong',
                'code' => '09',
                'response' => $exception->getMessage()
            ]);
            report($exception);
        }
    }
}
