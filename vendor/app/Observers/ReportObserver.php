<?php

namespace App\Observers;

use App\Http\Services\AdminNotificationService;
use App\Models\Report;

class ReportObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Handle the Report "creating" event.
     */
    public function creating(Report $report): void
    {
        $report->external_id = uniqid('R');
    }

    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        $this->notificationService->sendNotification('reportMade');
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "restored" event.
     */
    public function restored(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "force deleted" event.
     */
    public function forceDeleted(Report $report): void
    {
        //
    }
}
