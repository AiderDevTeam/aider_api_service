<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CampaignImageUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $data;
    private $campaign;
    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, $data)
    {
        //
        $this->data = $data;
        $this->campaign = $campaign;
        $this->runProc();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }

    private function runProc(): void
    {
        foreach($this->data['campaign_images'] as $image){
            try{
                logger('### CAMPAIGN IMAGE UPLOAD JOB RUNNING ###');
                logger()->info('http://api-gateway/api/file-upload');
                //http://174.138.80.217/api-gateway/api/file-upload
                $request = Http::withHeaders(jsonHttpHeaders())
                    ->post( 'http://api-gateway/api/file-upload', [
                        'image' => true,
                        'file' => $image,
                    ]);
                logger([
                    'status' => $request->status(),
                    'code' => $request->successful(),
                    'response' => $request
                ]);
                if($this->campaign->campaign_images == null){
                    $images = $request->json()['data']['file']['url'];
                }else{
                    $images = $this->campaign->campaign_images.','.$request->json()['data']['file']['url'];
                }
                if($request->successful()){
                    $this->campaign->campaign_images = $images;
                    $this->campaign->save();
                } 
    
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
}
