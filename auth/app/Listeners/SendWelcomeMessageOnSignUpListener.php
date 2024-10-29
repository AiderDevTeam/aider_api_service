<?php

namespace App\Listeners;

use App\Events\SendWelcomeMessageOnSignUpEvent;
use App\Jobs\SmsNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeMessageOnSignUpListener
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
    public function handle(SendWelcomeMessageOnSignUpEvent $event): void
    {
        logger('### NEW USER WELCOME MESSAGE EVENT FIRED ###');
        $user = $event->user;
        $welcomeMessage = "Welcome to the Poynt Community. Your go-to hub for selling your items, earning extra cash, and shopping safely online. Check out this welcome video on what to expect. https://bit.ly/WelcometoPoynt.\n\nAnd be sure to join our community to have access to interact with the team! https://bit.ly/PoyntCommunity";
        SmsNotificationJob::dispatch($user, null, $welcomeMessage)->onQueue('high')->delay(now()->addSecond());
    }
}
