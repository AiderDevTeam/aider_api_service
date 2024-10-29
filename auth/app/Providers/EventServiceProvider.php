<?php

namespace App\Providers;

use App\Events\AccountDeactivationEvent;
use App\Events\IdVerificationStatusUpdatedEvent;
use App\Events\RegisterUserOnPaymentServiceEvent;
use App\Events\SaveIdVerificationLogEvent;
use App\Events\SendWelcomeMessageOnSignUpEvent;
use App\Events\UpdateVendorUserEvent;
use App\Events\UserIdentificationStatusUpdatedEvent;
use App\Listeners\AccountDeactivationListener;
use App\Listeners\IdVerificationStatusUpdatedListener;
use App\Listeners\RegisterUserOnPaymentServiceListener;
use App\Listeners\SaveIdVerificationLogListener;
use App\Listeners\SendWelcomeMessageOnSignUpListener;
use App\Listeners\UpdateVendorUserListener;
use App\Listeners\UserIdentificationStatusUpdatedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RegisterUserOnPaymentServiceEvent::class => [
            RegisterUserOnPaymentServiceListener::class
        ],
        SaveIdVerificationLogEvent::class => [
            SaveIdVerificationLogListener::class
        ],
        SendWelcomeMessageOnSignUpEvent::class => [
            SendWelcomeMessageOnSignUpListener::class
        ],
        IdVerificationStatusUpdatedEvent::class => [
            IdVerificationStatusUpdatedListener::class
        ],
        UpdateVendorUserEvent::class => [
            UpdateVendorUserListener::class
        ],
        AccountDeactivationEvent::class => [
            AccountDeactivationListener::class
        ],
        UserIdentificationStatusUpdatedEvent::class => [
            UserIdentificationStatusUpdatedListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
