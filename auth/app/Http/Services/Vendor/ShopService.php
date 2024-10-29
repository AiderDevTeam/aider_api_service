<?php

namespace App\Http\Services\Vendor;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShopService
{
    private readonly string $bearerToken;

    public function __construct(private readonly array $requestPayload, private readonly ?User $user = null)
    {
        if (!is_null($user)) {
            $this->bearerToken = JWTAuth::fromUser($this->user);
        }
    }

    public function checkShopTagExistence(): bool
    {
        try {
            logger('### SENDING CHECK SHOP TAG EXISTENCE REQUEST TO VENDOR SERVICE ###');
            logger($data = ['shopTag' => $this->requestPayload['shopTag']]);

            $request = Http::post('http://vendor/api/sys/check-shop-tag-existence', $data);
            logger($request->json());

            return $request->successful();

        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public function updateClosetShopTag(): void
    {
        try {
            logger('### SENDING UPDATE CLOSET SHOP TAG REQUEST TO VENDOR SERVICE ###');
            logger($data = ['shopTag' => $this->requestPayload['shopTag']]);

            $request = Http::withHeaders(jsonHttpHeaders())->put('http://vendor/api/sys/update-closet-shoptag/' . $this->requestPayload['oldShopTag'], $data);

            logger()->info('### RESPONSE FROM VENDOR SERVICE ###');
            logger($request);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    public function createShop(): void
    {
        try {
            logger()->info('### SENDING CREATE SHOP REQUEST TO VENDOR SERVICE ###');
            logger($this->requestPayload);
            logger($url = "http://vendor/api/create-shop");

            $response = Http::withHeaders(jsonHttpHeaders())->withToken($this->bearerToken)
                ->post($url, $this->requestPayload);

            logger()->info('### RESPONSE FROM VENDOR SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
