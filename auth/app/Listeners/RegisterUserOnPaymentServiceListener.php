<?php

namespace App\Listeners;

use App\Events\RegisterUserOnPaymentServiceEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterUserOnPaymentServiceListener
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
    public function handle(RegisterUserOnPaymentServiceEvent $event): void
    {
        $user = $event->user;
        logger()->info('### REGISTERING USER ON PAYMENT SERVICE ###');
        try {
            Http::withToken($user->token)->withHeaders(jsonHttpHeaders())->post('http://payment/api/user');
            logger()->info('### USER REGISTERED ON PAYMENT SERVICE ###');
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
