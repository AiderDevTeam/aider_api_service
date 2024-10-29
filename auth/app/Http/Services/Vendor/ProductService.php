<?php

namespace App\Http\Services\Vendor;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ProductService
{
    public function __construct(public array $request)
    {
    }

    public function updateProduct(): PromiseInterface|Response|null
    {
        try {
            $data = $this->request;

            logger('### DISPATCHING PRODUCT UPDATE REQUEST TO VENDOR SERVICE ###');
            logger($url = 'http://vendor/api/sys/product/' . $data['productExternalId']);
            logger($data);

            $response = Http::withHeaders(jsonHttpHeaders())
                ->put($url, $data);

            logger('### VENDOR SERVICE RESPONSE ###');
            logger($response);

            return $response;
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
