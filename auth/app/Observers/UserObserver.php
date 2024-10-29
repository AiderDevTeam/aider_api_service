<?php

namespace App\Observers;

use App\Events\UpdateVendorUserEvent;
use App\Models\User;
use Illuminate\Http\Request;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        $user->external_id = $user->generateExternalId();
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
//        event(new SendWelcomeMessageOnSignUpEvent($user));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        event(new UpdateVendorUserEvent($user));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
