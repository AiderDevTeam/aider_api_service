<?php

namespace App\Console\Commands;

use App\Models\ProductPhoto;
use App\Models\Vendor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImageDownloadCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imagedownload:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends Image URLs to New Image Service and then retrieves and stores new url';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
       
       // $this->runProcForProductPhotos();
        //$this->runProcForVendors();
    }


    public function runProcForVendors() : void{
        logger('### Start UPLOAD IMAGE SERVICE');
        Vendor::where('shop_logo_url_b_status','=','false')
        ->where('shop_logo_url', '!=', '')
        ->chunk(150, function($vendors) {
            foreach ($vendors as $vendor) {
                logger('### VENDOR::');
                logger($vendor->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $vendor->shop_logo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $vendor->update(['shop_logo_url_b' => $response->data->ImageUrl, 'shop_logo_url_b_status' => 'true']);
                }
            }
        });
    
    }

    public function runProcForProductPhotos() : void{
        ProductPhoto::where('photoUrl_status','false')->chunk(150, function($photos) {
            logger('### START PRODUCT PHOTOS UPLOAD IMAGE SERVICE');
            foreach ($photos as $photo) {
                logger('### PRODUCT PHOTOS::');
                logger($photo->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image',[
                    "imageUrl" => $photo->photoUrl
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    logger('### SEND TO IMAGE SERVICE::');
                    $photo->update(['photoUrl_b' => $response->data->ImageUrl, 'photoUrl_status' => 'true']);
                }
            }
        });
    
    }
}
