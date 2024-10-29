<?php

namespace App\Listeners;

use App\Custom\Identification;
use App\Events\UserIdentificationStatusUpdatedEvent;
use App\Http\Resources\UserIdentificationResource;
use App\Jobs\PushNotificationJob;
use App\Models\User;
use App\Models\UserIdentification;
use Exception;

class UserIdentificationStatusUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserIdentificationStatusUpdatedEvent $event): void
    {
        logger('### USER IDENTIFICATION STATUS UPDATED EVENT TRIGGERED ###');

        $user = $event->userIdentification->user;

        $this->notifyUserOnVerificationStatusChange($user, $event->userIdentification);

        try {
            if ($user->hasCompletedKYC()) {

                logger()->info('### KYC COMPLETED --- UPDATING USER ID VERIFICATION STATUS TO COMPLETED ###');

                $user->update([
                    'id_verification_status' => 'completed',
                    'id_verified' => true,
                    'id_verified_at' => now()
                ]);
                return;
            }

            logger()->info('### KYC NOT COMPLETED --- UPDATING USER ID VERIFICATION STATUS ###');

            $user->update([
                'id_verification_status' => 'pending',
                'id_verified' => false
            ]);

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function notifyUserOnVerificationStatusChange(User $user, UserIdentification $userIdentification): void
    {
        try {
            $message = match ($userIdentification->status) {
                Identification::STATUS['ACCEPTED'] => "Identification verified!🎉 You can now rent and list items. Happy exploring!😊",
                Identification::STATUS['REJECTED'] => "Oops! ID verification was not successful. Double-check the details and try again.",
                default => null
            };

            if (is_null($message))
                return;

            PushNotificationJob::dispatch($user, [
                'title' => 'User Identification',
                'body' => $message,
                'data' => array(
                    'action' => 'kyc',
                    'resource' => json_encode(new UserIdentificationResource($userIdentification))
                )
            ])->onQueue('high');

        } catch (Exception $exception) {
            report($exception);
        }
    }
}
