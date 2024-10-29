<?php

namespace App\Listeners;

use App\Custom\Status;
use App\Events\IdVerificationStatusUpdatedEvent;
use App\Http\Services\API\IdVerificationService;
use App\Jobs\PushNotificationJob;
use App\Jobs\SmsNotificationJob;
use App\Models\IdVerificationLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IdVerificationStatusUpdatedListener
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
    public function handle(IdVerificationStatusUpdatedEvent $event): void
    {
        logger('### ID VERIFICATION STATUS UPDATED EVENT TRIGGERED ###');

        $user = $event->user->refresh();
        if ($user->id_verification_status === Status::PENDING)
            return;

        $rejectionReason = $user->idVerificationLogs()->latest()?->first()?->response;

        $successfulKYCMessage = "Congratulations! Your identification has been verified successfully. You're all set to start selling on the Aidar app. List your items now and reach more customers.\n#StayonPoynt";
        $rejectedKYCMessage = self::getRejectionReasonNotificationMessage($user, $rejectionReason);

        $statusChangeMessage = match ($user->id_verification_status) {
            Status::REJECTED => $rejectedKYCMessage,
            Status::APPROVED => $successfulKYCMessage,
            default => null
        };

        if (!is_null($statusChangeMessage)) {
            PushNotificationJob::dispatch($user, [
                'title' => "KYC $user->id_verification_status",
                'body' => $statusChangeMessage,
                'data' => array(
                    'action' => "kyc $user->id_verification_status",
                    'resource' => null
                )
            ]);

            SmsNotificationJob::dispatchSync($user, null, $statusChangeMessage);
        }
    }

    public static function getRejectionReasonNotificationMessage(User $user, ?string $rejectionReason): ?string
    {
        if (is_null($rejectionReason))
            return null;

        $firstName = $user->first_name;
        return match (strtolower($rejectionReason)) {
            'incorrect ghana card number' => "Hi $firstName!\nYour verification was unsuccessful due to an incorrect Ghana Card number. Kindly double-check and resubmit.\nThank you!",
            'blurry ghana card' => "Hi $firstName!\nYour verification was unsuccessful because the image of the Ghana card you submitted was blurry. Kindly take a clearer picture and resubmit.\nThank you!",
            'blurry selfie' => "Hi $firstName!\nYour verification was unsuccessful because the selfie taken is blurry. Kindly take a clearer selfie and resubmit.\nThank you!",
            'wrong ghana card image' => "Hi $firstName!\nYour verification was unsuccessful because you uploaded the wrong image of your Ghana card. Kindly double-check and resubmit.\nThank you!",
            default => null
        };
    }
}
