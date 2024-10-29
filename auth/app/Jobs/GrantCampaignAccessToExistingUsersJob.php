<?php

namespace App\Jobs;

use App\Models\ProductTag;
use App\Models\User;
use App\Models\UserCampaignAccess;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GrantCampaignAccessToExistingUsersJob implements ShouldQueue
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
        logger('### GRANT EXISTING USER BLACK TICKET CAMPAIGN ACCESS - STARTED ###');
        try {
            User::query()->chunk(10, function ($users) {
                foreach ($users as $user) {

                    logger("### GRANTING BLACK TICKET ACCESS TO [$user->external_id] ###");

                    if ($user->hasCampaignAccess(UserCampaignAccess::BLACK_TICKET))
                        continue;

                    $user->campaignAccesses()->create([
                        'external_id' => uniqid('CA'),
                        'campaign_type' => UserCampaignAccess::BLACK_TICKET
                    ]);

                    logger('### ACCESS GRANTED ###');
                }
            });

        } catch (Exception $exception) {
            report($exception);
        }
        logger('### GRANT EXISTING USER BLACK TICKET CAMPAIGN ACCESS - COMPLETED ###');

    }
}
