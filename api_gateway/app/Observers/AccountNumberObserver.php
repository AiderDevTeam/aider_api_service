<?php

namespace App\Observers;

use App\Models\BankAccount;

class AccountNumberObserver
{
    /**
     * Handle the BankAccount "creating" event.
     */
    public function creating(BankAccount $accountNumber): void
    {
        $accountNumber->external_id = uniqid();
    }

    /**
     * Handle the BankAccount "created" event.
     */
    public function created(BankAccount $accountNumber): void
    {
        //
    }

    /**
     * Handle the BankAccount "updated" event.
     */
    public function updated(BankAccount $accountNumber): void
    {
        //
    }

    /**
     * Handle the BankAccount "deleted" event.
     */
    public function deleted(BankAccount $accountNumber): void
    {
        //
    }

    /**
     * Handle the BankAccount "restored" event.
     */
    public function restored(BankAccount $accountNumber): void
    {
        //
    }

    /**
     * Handle the BankAccount "force deleted" event.
     */
    public function forceDeleted(BankAccount $accountNumber): void
    {
        //
    }
}
