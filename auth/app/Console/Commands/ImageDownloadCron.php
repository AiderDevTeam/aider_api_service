<?php

namespace App\Console\Commands;

use App\Models\User;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       //$this->selfieUrl();
    }


    private function downloadProfile(){
        logger('### Start UPLOAD IMAGE SERVICE');
        User::where('profile_photo_url_status','=','false')
        ->where('profile_photo_url', '!=', '')
        ->chunk(150, function($users) {
            foreach ($users as $user) {
                logger('### USERS::');
                logger($user->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $user->profile_photo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $user->update(['profile_photo_url_b' => $response->data->ImageUrl, 'profile_photo_url_status' => 'true']);
                }
            }
        });
    }


    private function saveIdPhotoUrl(){
        logger('### Start UPLOAD IMAGE SERVICE');
        User::where('profile_photo_url_status','=','false')
        ->where('id_photo_url', '!=', '')
        ->chunk(150, function($users) {
            foreach ($users as $user) {
                logger('### USERS::');
                logger($user->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $user->profile_photo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $user->update(['id_photo_url' => $response->data->ImageUrl, 'profile_photo_url_status' => 'true']);
                }
            }
        });
    }

    private function signatureUrl(){
        logger('### Start UPLOAD IMAGE SERVICE');
        User::where('profile_photo_url_status','=','false')
        ->where('signature_url', '!=', '')
        ->chunk(150, function($users) {
            foreach ($users as $user) {
                logger('### USERS::');
                logger($user->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $user->profile_photo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $user->update(['signature_url' => $response->data->ImageUrl, 'profile_photo_url_status' => 'true']);
                }
            }
        });
    }

    private function savePhotoOnId(){
        logger('### Start UPLOAD IMAGE SERVICE');
        User::where('profile_photo_url_status','=','false')
        ->where('photo_on_id_url', '!=', '')
        ->chunk(150, function($users) {
            foreach ($users as $user) {
                logger('### USERS::');
                logger($user->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $user->profile_photo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $user->update(['photo_on_id_url' => $response->data->ImageUrl, 'profile_photo_url_status' => 'true']);
                }
            }
        });
    }


    private function selfieUrl(){
        logger('### Start UPLOAD IMAGE SERVICE');
        User::where('profile_photo_url_status','=','false')
        ->where('selfie_url', '!=', '')
        ->chunk(150, function($users) {
            foreach ($users as $user) {
                logger('### USERS::');
                logger($user->toArray());
                $sendToImageService = Http::post('http://35.193.156.242/api/download/image', [
                    "imageUrl" => $user->profile_photo_url
                ]);
                logger('### SEND TO IMAGE SERVICE::');
                logger($sendToImageService->json());
                $response = json_decode(json_encode($sendToImageService->json()));
                if( !empty($response->status) && $response->status == 201 ){
                    $user->update(['selfie_url' => $response->data->ImageUrl, 'profile_photo_url_status' => 'true']);
                }
            }
        });
    }
}
