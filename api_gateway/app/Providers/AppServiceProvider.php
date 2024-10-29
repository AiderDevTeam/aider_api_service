<?php

namespace App\Providers;

use App\Models\BankAccount;
use App\Models\Delivery;
use App\Models\Processor;
use App\Models\Transaction;
use App\Observers\AccountNumberObserver;
use App\Observers\DeliveryObserver;
use App\Observers\ProcessorObserver;
use App\Observers\TransactionObserver;
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
        Processor::Observe(ProcessorObserver::class);
        Transaction::Observe(TransactionObserver::class);
        BankAccount::Observe(AccountNumberObserver::class);
        Delivery::Observe(DeliveryObserver::class);
    }
}
