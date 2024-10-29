<?php

namespace App\Observers;

use App\Models\SubCategory;

class SubCategoryObserver
{
    /**
     * Handle the SubCategory "created" event.
     */
    public function creating(SubCategory $subCategory): void
    {
        $subCategory->external_id = uniqid('SC');
    }

    /**
     * Handle the SubCategory "created" event.
     */
    public function created(SubCategory $subCategory): void
    {

    }

    /**
     * Handle the SubCategory "updated" event.
     */
    public function updated(SubCategory $subCategory): void
    {

    }

    /**
     * Handle the SubCategory "deleted" event.
     */
    public function deleted(SubCategory $subCategory): void
    {
        //
    }

    /**
     * Handle the SubCategory "restored" event.
     */
    public function restored(SubCategory $subCategory): void
    {
        //
    }

    /**
     * Handle the SubCategory "force deleted" event.
     */
    public function forceDeleted(SubCategory $subCategory): void
    {
        //
    }
}
