<?php

namespace App\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class PayoutWalletListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $wallet = $event->wallet;
        $bearerToken = $event->request->bearerToken();



//        $userHadPayoutWallet = false;
//
//
//        if ($user->getPayoutWallet()) {
//            $userHadPayoutWallet = true;
//        }

        setPayoutWallet($wallet);


        logger("USER NOW HAS A PAYOUT WALLET");
        logger("\n--- REGISTERING PAYOUT WALLET STATUS ON AUTH ---");
        try {
            $response = Http::withToken($bearerToken)
                ->withHeaders(jsonHttpHeaders())
                ->put('auth/api/user', [
                    "hasPayoutWallet" => true
                ]);

            logger($response);
        } catch (Exception $exception) {
            report($exception);
        }


    }
}
