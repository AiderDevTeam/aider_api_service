<?php

namespace App\Http\Services\Api;

use App\Http\Requests\StoreVendorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WalletService
{
    public static function create(Request $request, StoreVendorRequest $vendorRequest)
    {
        logger('### DISPATCHING CREATE PAYOUT WALLET REQUEST TO PAYMENT SERVICE ###');
        logger($url = 'http://payment/api/wallets/create');
        $response = Http::withToken($request->bearerToken())->post($url, [
            'type' => "momo",
            'accountNumber' => $vendorRequest->accountNumber,
            'sortCode' => $vendorRequest->sortCode,
            'accountName' => $vendorRequest->accountName,
            'payout' => true
        ]);

        logger($response);

    }
}
