<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\ProductAddress;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserIdentification;
use App\Observers\AddressObserver;
use App\Observers\AdminObserver;
use App\Observers\UserIdentificationObserver;
use App\Observers\UserObserver;
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
        User::observe(UserObserver::class);
        Admin::observe(AdminObserver::class);
        Address::observe(AddressObserver::class);
        UserIdentification::observe(UserIdentificationObserver::class);
    }
}
