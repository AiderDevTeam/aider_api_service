<?php

namespace App\Providers;

use App\Models\AdminMetric;
use App\Models\Bank;
use App\Models\DeliveryPayment;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VASPayment;
use App\Models\Wallet;
use App\Observers\AdminMetricObserver;
use App\Observers\BankObserver;
use App\Observers\DeliveryPaymentObserver;
use App\Observers\PaymentObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use App\Observers\VASPaymentObserver;
use App\Observers\WalletObserver;
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
        Transaction::observe(TransactionObserver::class);
        Payment::observe(PaymentObserver::class);
        VASPayment::observe(VASPaymentObserver::class);
        DeliveryPayment::observe(DeliveryPaymentObserver::class);
        User::observe(UserObserver::class);
        Wallet::observe(WalletObserver::class);
        AdminMetric::observe(AdminMetricObserver::class);
        Bank::observe(BankObserver::class);
    }
}
