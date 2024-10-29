<?php

namespace App\Jobs\CustomJobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordPendingBookingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            logger('### JOB TO RECORD USERS PENDING BOOKINGS RUNNING ###');

            User::query()->chunk(10, function ($users) {
                foreach ($users as $user) {
//                    $user->recordVendorBookingsPendingPickupCount();
//                    $user->recordRenterBookingsPendingPickupCount();
                    $user->recordVendorBookingPendingAcceptance();
                    manuallySyncModels([$user]);
                }
            });

        } catch (Exception $exception) {
            report($exception);
        }
        logger('### JOB TO RECORD USERS PENDING BOOKINGS COMPLETED ###');
    }
}
