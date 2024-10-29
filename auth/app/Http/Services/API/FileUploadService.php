<?php

namespace App\Http\Services\API;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public static function upload(string $base64String, bool $isImage)
    {
        try {
            logger('### SENDING FILE UPLOAD REQUEST TO API GATEWAY SERVICE ###');
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('api-gateway/api/file-upload', [
                    'image' => $isImage,
                    'file' => $base64String,
                ]);
            logger("### FILE UPLOAD RESPONSE ###");
            logger($request);
            if ($request->successful()) return $request->json()['data']['file'];
        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public static function uploadToImageService(string $imagePath): ?string
    {
        logger('### DISPATCHING IMAGE UPLOAD REQUEST TO IMAGE SERVICE ###');
        $imageFullPath = Storage::disk('local')->path($imagePath);
        try {

            $client = new Client();

            $request = new Request('POST', 'http://35.193.156.242/api/store/image');

            $response = $client->sendAsync($request, [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => Utils::tryFopen($imageFullPath, 'r'),
                        'filename' => $imageFullPath,
                        'headers' => [
                            'Content-Type' => '<Content-type header>'
                        ]
                    ]
                ]
            ])->wait();

            logger('### RESPONSE FROM IMAGES SERVICE ###', [$response = json_decode($response->getBody()->getContents())]);
            deleteFile($imagePath);

            return (isset($response->status) && $response->status == 201) ? $response->data->ImageUrl : null;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }

    public static function base64StringToFile(string $base64String)
    {
        logger('### DISPATCHING BASE64 STRING TO FILE REQUEST TO IMAGE SERVICE ###');
        logger($url = 'https://image.itspoynt.shop/api/convert/image');
        try {
            $response = Http::withHeaders(jsonHttpHeaders())->post($url, [
                'file' => $base64String
            ]);

            logger('### RESPONSE FROM IMAGE SERVICE ###');
            logger($response);

            if ($response->successful()) {
                return $response->json()['data']['file']['url'] ?? null;
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
