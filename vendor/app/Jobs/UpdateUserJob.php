<?php

namespace App\Jobs;

use App\Http\Services\GetAuthUserService;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUserJob implements ShouldQueue
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
            logger()->info('### UPDATE USER DETAILS ON VENDOR JOB --- STARTED ###');

            User::query()->chunk(5, function ($users) {
                foreach ($users as $user) {
                    logger('### User:::::: ', [$user->external_id]);
                    $authUser = GetAuthUserService::getUser($user->external_id);

                    sleep(5);

                    $user->update([
                        'other_details' => $authUser
                    ]);
                }
            });

        } catch (Exception $exception) {
            report($exception);
        }
        logger()->info('### UPDATE USER DETAILS ON VENDOR JOB --- COMPLETED ###');
    }
}
