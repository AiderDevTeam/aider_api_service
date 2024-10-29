<?php

namespace App\Providers;

use App\Events\MakeSubtractionOperationEvent;
use App\Events\ReferralCashRewardNotificationEvent;
use App\Events\StoreRewardValueEvent;
use App\Listeners\MakeSubtractionOperationListener;
use App\Listeners\ReferralCashRewardNotificationListener;
use App\Listeners\StoreRewardValueListener;
use App\Models\Campaign;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Observers\CampaignObserver;
use App\Observers\ReferralObserver;
use App\Observers\ReferralRewardObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],

        StoreRewardValueEvent::class => [
            StoreRewardValueListener::class
        ],
        ReferralCashRewardNotificationEvent::class => [
            ReferralCashRewardNotificationListener::class
        ],
        MakeSubtractionOperationEvent::class =>[
            MakeSubtractionOperationListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Campaign::observe(CampaignObserver::class);
        Referral::observe(ReferralObserver::class);
        ReferralReward::observe(ReferralRewardObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
