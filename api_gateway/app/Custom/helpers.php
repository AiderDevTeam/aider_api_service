<?php

use App\Models\Delivery;
use App\Models\File;
use App\Models\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils as Psr7Utils;
use GuzzleHttp\Utils;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function successfulJsonResponse(mixed $data = [], string $message = 'Request processed successfully', $statusCode = 200): JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data
    ], $statusCode);
}

function errorJsonResponse(array $errors = [], string $message = 'Something went wrong, please try again later', $statusCode = 500): JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ], $statusCode);
}

function arrayKeyToSnakeCase(array $data): array
{
    $newData = [];
    foreach ($data as $key => $datum) {
        $newData[Str::snake($key)] = $datum;
    }
    return $newData;
}

function jsonHttpHeaders(): array
{
    return [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];
}

function transactionPendingMessage(): string
{
    return 'pending authorization';
}

function toFloat($number): float
{
    $string = number_format($number, 2);
    return (float)str_replace(',', '', $string);
}

function transactionToArray(Transaction $transaction): array
{
    return [
        'status' => $transaction->status,
        'transactionId' => $transaction->external_id,
        'type' => $transaction->type,
        'stan' => $transaction->stan,
        'amount' => (int)($transaction->amount / 100),
        'updatedAt' => $transaction->updated_at->toDateTimeString(),
        'responseCode' => $transaction->response_code,
        'code' => $transaction->response_code,
        'responseMessage' => $transaction->response_message,
    ];
}

function base64StringToFile(string $base64String, string $filename, Illuminate\Contracts\Filesystem\Filesystem $disk = null): string
{
    $disk = $disk ?: Storage::disk('local');

    logger()->info('### START WRITING UPLOADED FILE ###');
    $path = $disk->put($path = 'public/temp/' . $filename, base64_decode($base64String)) ? $path : '';
    logger()->info('### COMPLETE WRITING UPLOADED FILE ###');

    return $path;
}

function createFile(string $path, string $filename, Illuminate\Contracts\Filesystem\Filesystem $disk): Model|Builder
{
    logger()->info('### INSERTING UPLOADED FILE ###');
    return File::query()->create([
        'name' => $filename,
        'size' => $disk->size($path),
        'mime' => $disk->mimeType($path),
        'path' => $path,
        'external_id' => uniqid()
    ]);
}

function updateAndCleanFile(Model $file, string $path, Illuminate\Contracts\Filesystem\Filesystem $disk): void
{
    logger()->info('### UPDATING FILE ###');
    $file->update([
        //'url' => (env('APP_ENV')  == 'local') ? sendFileByCurl(Storage::disk('local')->path($path)) : cloudinary()->upload(Storage::disk('local')->path($path))->getSecurePath()
        'url' => sendFileByCurl(Storage::disk('local')->path($path))
    ]);

    //deleteLocalFile($disk, $path);
}

function sendFileByCurl(string $path){

    logger()->info('### UPLOADING FILE VIA CURL REQUEST ###');
    logger($path);

    $client = new Client();
    $options = [
      'multipart' => [
        [
          'name' => 'image',
          'contents' => Psr7Utils::tryFopen($path, 'r'),
          'filename' => $path,
          'headers'  => [
            'Content-Type' => '<Content-type header>'
          ]
        ]
    ]];
    $request = new Request('POST', 'http://35.193.156.242/api/store/image');
    $res = $client->sendAsync($request, $options)->wait();
    $res = $res->getBody()->getContents();
    $decode = json_decode($res);
    logger()->info('### UPLOADING FILE VIA CURL RESPONSE ###');
    logger($res);

    if(!empty($decode->status == 201)){
        return $decode->data->ImageUrl;
    }else{
        return "";
    }
}


function deleteLocalFile(Illuminate\Contracts\Filesystem\Filesystem $disk, string $path): void
{
    logger()->info("### DELETING LOCAL FILE @ {$path}  ###");
    $disk->delete($path);
    logger()->info('### LOCAL FILE DELETED ###');
}

function base64ToCloudStorage(string $base64String, string $filename, string $disk = 'local'): Model|Builder
{
    $path = base64StringToFile($base64String, $filename, $localDisk = Storage::disk($disk));
    $file = createFile($path, $filename, $localDisk);

    updateAndCleanFile($file, $path, $localDisk);

    return $file;
}

function deliveryToArray(Delivery $delivery): array
{
    return [
        'externalId' => $delivery->external_id,
        'status' => $delivery->status,
        'trackingNumber' => $delivery->tracking_number,
        'currency'=> $delivery->currency,
        'deliveryOption' => $delivery->delivery_option,
        'isPickUp' => (boolean)$delivery->is_pickup,
        'isFulfillmentDelivery' => (boolean)$delivery->is_fulfillment_delivery,
        'amountToCollect' => $delivery->amount_to_collect,
        'serviceType' => $delivery->service_type,
        'isPrepaidDelivery' => (boolean)$delivery->is_prepaid_delivery
    ];
}

