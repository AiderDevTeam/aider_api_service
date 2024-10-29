<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SubCategoryFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subCategoryRequest;
    private $subCategory;
    /**
     * Create a new job instance.
     */
    public function  __construct($subCategoryRequest,$subCategory)
    {
        //
        $this->subCategoryRequest = $subCategoryRequest;
        $this->subCategory = $subCategory;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->runProc();
    }

    private function runProc(): void
    {
        try{
            logger('### SUBCATEGORY FILE UPLOAD JOB RUNNING ###');
            logger()->info('http://api-gateway/api/file-upload');
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post( 'http://api-gateway/api/file-upload', [
                    'image' => true,
                    'file' => $this->subCategoryRequest['imageUrl'],
                ]);
            logger([
                'status' => $request->status(),
                'code' => $request->successful(),
                'response' => $request->json()
            ]);
            if($request->successful()) $this->subCategory->update(['image_url' => $request->json()['data']['file']['url']]);

        }catch(\Exception $exception){
            logger([
                'status' => 'Something went wrong',
                'code' => '09',
                'response' => $exception->getMessage()
            ]);
            report($exception);
        }
    }
}
