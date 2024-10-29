<?php

namespace App\Observers;

use App\Models\AdminMetric;

class AdminMetricObserver
{
    public function creating(AdminMetric $adminMetric): void
    {
        $adminMetric->external_id = 'payment';
    }
}
