<?php

namespace App\Providers;

use App\Models\ActionPoynt;
use App\Models\UserActionPoynt;
use App\Observers\ActionPoyntObserver;
use App\Observers\UserActionPoyntObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ActionPoynt::observe(ActionPoyntObserver::class);
        UserActionPoynt::observe(UserActionPoyntObserver::class);
    }
}
