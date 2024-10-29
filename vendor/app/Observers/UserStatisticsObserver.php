<?php

namespace App\Observers;

use App\Models\UserStatistics;

class UserStatisticsObserver
{
    /**
     * Handle the UserStatistics "created" event.
     */
    public function created(UserStatistics $userStatistics): void
    {
    }

    /**
     * Handle the UserStatistics "updated" event.
     */
    public function updated(UserStatistics $userStatistics): void
    {
    }

    /**
     * Handle the UserStatistics "deleted" event.
     */
    public function deleted(UserStatistics $userStatistics): void
    {
        //
    }

    /**
     * Handle the UserStatistics "restored" event.
     */
    public function restored(UserStatistics $userStatistics): void
    {
        //
    }

    /**
     * Handle the UserStatistics "force deleted" event.
     */
    public function forceDeleted(UserStatistics $userStatistics): void
    {
        //
    }
}
