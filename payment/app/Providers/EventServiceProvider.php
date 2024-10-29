<?php

namespace App\Providers;

use App\Events\CollectionPaymentStatusUpdateEvent;
use App\Events\DisbursementStatusUpdateEvent;
use App\Events\PayoutWalletEvent;
use App\Events\SuccessfulReversalEvent;
use App\Events\SuccessfulTransactionNotificationEvent;
use App\Events\ScoreUserPoyntAfterTransactionEvent;
use App\Listeners\CollectionPaymentStatusUpdateListener;
use App\Listeners\DisbursementStatusUpdateLIstener;
use App\Listeners\PayoutWalletListener;
use App\Listeners\SuccessfulReversalListener;
use App\Listeners\SuccessfulTransactionNotificationListener;
use App\Listeners\ScoreUserPoyntAfterTransactionListener;
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
        'App\Events\SuccessfulDisbursementEvent' => [
            'App\Listeners\SuccessfulDisbursementListener'
        ],
        'App\Events\FailedDisbursementEvent' => [
            'App\Listeners\FailedDisbursementListener'
        ],
        'App\Events\SuccessfulCollectionEvent' => [
            'App\Listeners\SuccessfulCollectionListener'
        ],
        'App\Events\FailedCollectionEvent' => [
            'App\Listeners\FailedCollectionListener'
        ],
        SuccessfulTransactionNotificationEvent::class => [
            SuccessfulTransactionNotificationListener::class
        ],
        ScoreUserPoyntAfterTransactionEvent::class => [
            ScoreUserPoyntAfterTransactionListener::class
        ],
        SuccessfulReversalEvent::class => [
            SuccessfulReversalListener::class
        ],
        PayoutWalletEvent::class => [
            PayoutWalletListener::class
        ],
        CollectionPaymentStatusUpdateEvent::class => [
            CollectionPaymentStatusUpdateListener::class
        ],
        DisbursementStatusUpdateEvent::class => [
            DisbursementStatusUpdateLIstener::class
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
