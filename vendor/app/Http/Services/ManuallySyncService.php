<?php

namespace App\Http\Services;


use Illuminate\Support\Facades\Http;

class ManuallySyncService
{
    public static function manualSync(array $request): void
    {
        $url = env('FIRESTORE_SERVER_URI');
        $response = Http::withHeaders(jsonHttpHeaders())->post($url,[
            'externalId' => $request['externalId'],
            'collection' => $request['collection'],
            'data' => $request['data']
        ]);

        logger($response);
    }
}
