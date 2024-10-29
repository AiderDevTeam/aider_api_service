<?php

namespace App\Listeners;

use App\Events\PoyntScoreNotificationEvent;
use App\Http\Services\NotificationService;
use App\Models\UserActionPoynt;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Pluralizer;

class PoyntScoreNotificationListener
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
    public function handle(PoyntScoreNotificationEvent $event): void
    {
        $userActionPoynt = $event->userActionPoynt;
        $action = $userActionPoynt->actionPoynt->action;
        $poyntEarned = round(($userActionPoynt->poynt * $userActionPoynt->action_value), 0, PHP_ROUND_HALF_DOWN);
        try {
            if ($poyntEarned >= 1 && $userActionPoynt->type === UserActionPoynt::ACTION_POYNT_TYPES['CREDIT']) {
                logger()->info('### POYNT SCORE NOTIFICATION EVENT TRIGGERED ###');
                $notificationData = [
                    'title' => 'Poynt Earned',
                    'body' => "Awesome🥳! You've earned $poyntEarned ". Pluralizer::plural('poynt', $poyntEarned)." for $action! Keep it up!",
                    'data' => json_encode([
                        'action' => $action,
                        'poyntEarned' => $poyntEarned
                    ]),
                    'notificationAction' => 'poynt',
                    'userExternalId' => $userActionPoynt->user->external_id
                ];

                (new NotificationService($notificationData))->sendPush();
            }
        } catch (Exception $exception) {
            report($exception);
        }
    }
}
