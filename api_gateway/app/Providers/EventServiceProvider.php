<?php

namespace App\Providers;

use App\Events\DeliveryUpdatedEvent;
use App\Events\ProcessDeliveryEvent;
use App\Events\TransactionCreatedEvent;
use App\Events\TransactionUpdatedEvent;
use App\Events\UpdateOrCreateIdVerificationEvent;
use App\Listeners\DeliveryUpdatedListener;
use App\Listeners\ProcessDeliveryListener;
use App\Listeners\TransactionCreatedListener;
use App\Listeners\TransactionUpdatedListener;
use App\Listeners\UpdateOrCreateIdVerificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        TransactionCreatedEvent::class => [
            TransactionCreatedListener::class
        ],
        TransactionUpdatedEvent::class => [
            TransactionUpdatedListener::class
        ],
        UpdateOrCreateIdVerificationEvent ::class => [
            UpdateOrCreateIdVerificationListener::class
        ],
        ProcessDeliveryEvent ::class => [
            ProcessDeliveryListener::class
        ],
        DeliveryUpdatedEvent::class => [
            DeliveryUpdatedListener::class
        ],
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
