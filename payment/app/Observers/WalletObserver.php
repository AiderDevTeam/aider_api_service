<?php

namespace App\Observers;

use App\Jobs\TransferRecipientCodeGenerationJob;
use App\Models\Wallet;

class WalletObserver
{
    /**
     * Handle the Wallet "created" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function created(Wallet $wallet): void
    {
        TransferRecipientCodeGenerationJob::dispatch($wallet)->onQueue('high');
    }

    /**
     * Handle the Wallet "creating" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function creating(Wallet $wallet): void
    {
        $wallet->external_id = uniqid('W');
    }

    /**
     * Handle the Wallet "updated" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function updated(Wallet $wallet)
    {
        manuallySyncModels([$wallet->user]);
    }

    /**
     * Handle the Wallet "deleted" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function deleted(Wallet $wallet)
    {
        //
    }

    /**
     * Handle the Wallet "restored" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function restored(Wallet $wallet)
    {
        //
    }

    /**
     * Handle the Wallet "force deleted" event.
     *
     * @param Wallet $wallet
     * @return void
     */
    public function forceDeleted(Wallet $wallet)
    {
        //
    }
}
