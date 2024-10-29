<?php

namespace App\Http\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;

class PayoutWalletService
{
    public static function getWallet(string $userExternalId)
    {
        try {
            logger('### DISPATCHING GET PAYOUT WALLET REQUEST TO PAYMENT SERVICE ###');
            logger($url = "http://payment/api/sys/get-payout-wallet/$userExternalId");
            $response = Http::withHeaders(jsonHttpHeaders())->get($url);

            logger('### RESPONSE FROM PAYMENT SERVICE ###');
            logger($response);

            if ($response->successful()) return $response->json();
        } catch (Exception $exception) {
            report($exception);
        }
        return null;
    }
}
