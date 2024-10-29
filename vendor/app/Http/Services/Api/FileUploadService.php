<?php

namespace App\Http\Services\Api;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function __construct(private readonly string $base64String, private readonly bool $isImage)
    {
    }

    public function base64Upload()
    {
        try {
            $request = Http::withHeaders(jsonHttpHeaders())
                ->post('api-gateway/api/file-upload', [
                    'image' => $this->isImage,
                    'file' => $this->base64String,
                ]);
            if ($request->successful()) return $request->json()['data']['file'];

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public static function uploadToImageService(string $imagePath): ?string
    {
        logger('### SENDING IMAGE UPLOAD REQUEST TO IMAGE SERVICE ###');
        try {
            $imageFullPath = Storage::disk('local')->path($imagePath);


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

            deleteLocalFile($imagePath);

            return (isset($response->status) && $response->status == 201) ? $response->data->ImageUrl : null;

        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
