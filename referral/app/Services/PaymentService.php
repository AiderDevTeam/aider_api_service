<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public static function getWallet(string $userExternalId)
    {
        try {
            logger('### GETTING USER WALLET FROM PAYMENT SERVICE ###');
            logger($url = 'http://payment/api/sys/get-referral-payout-wallet/' . $userExternalId);

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
