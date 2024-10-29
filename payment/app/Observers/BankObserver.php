<?php

namespace App\Observers;

use App\Models\Bank;

class BankObserver
{
    /**
     * Handle the Bank "creating" event.
     */
    public function creating(Bank $bank): void
    {
        $bank->external_id = uniqid('BANK');
    }

    /**
     * Handle the Bank "updated" event.
     */
    public function updated(Bank $bank): void
    {
        //
    }

    /**
     * Handle the Bank "deleted" event.
     */
    public function deleted(Bank $bank): void
    {
        //
    }

    /**
     * Handle the Bank "restored" event.
     */
    public function restored(Bank $bank): void
    {
        //
    }

    /**
     * Handle the Bank "force deleted" event.
     */
    public function forceDeleted(Bank $bank): void
    {
        //
    }
}
