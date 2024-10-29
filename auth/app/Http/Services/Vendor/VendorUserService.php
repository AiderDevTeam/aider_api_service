<?php

namespace App\Http\Services\Vendor;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class VendorUserService
{
    public function __construct(private readonly User $user, private readonly array $requestPayload = [])
    {
    }

    public function sendAuthUserToVendor(): void
    {
        try {
            logger()->info('### SENDING UPDATED USER DETAILS REQUEST TO VENDOR SERVICE ###');

            $response = Http::withToken(
                JWTAuth::fromUser($this->user)
            )->withHeaders(jsonHttpHeaders())->get(
                env('VENDOR_BASE_URL') . '/api/user'
            );

            logger()->info('### RESPONSE FROM VENDOR SERVICE ###');
            logger($response);

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
