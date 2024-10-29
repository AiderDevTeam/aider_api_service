<?php

namespace App\Console;

use App\Jobs\FailTransactionsJob;
use App\Jobs\HandleFailedCollectionCallbackJob;
use App\Jobs\HandleFailedDisbursementCallbackJob;
use App\Jobs\ReversalJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->job(new FailTransactionsJob)->everyThirtyMinutes();
        $schedule->job(new ReversalJob())->everyFiveMinutes();
        $schedule->job(new HandleFailedDisbursementCallbackJob())->everyMinute();
        $schedule->job(new HandleFailedCollectionCallbackJob())->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
