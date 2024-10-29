<?php

namespace App\Listeners;

use App\Events\ConversationInitializationEvent;
use App\Http\Resources\ConversationResource;
use App\Http\Services\NotificationService;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ConversationInitializationListener
{

    const USER = 'user';
    const VENDOR = 'vendor';

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
    public function handle(ConversationInitializationEvent $event): void
    {
        $booking = $event->booking;
        logger("### INITIALIZING CONVERSATION FORM BOOKING [$booking->external_id] ###");

        try {

            $conversation = Conversation::create([
                'user_id' => $booking->user->id,
                'vendor_id' => $booking->vendor->id,
            ]);

            $message = $conversation->messages()->createQuietly([
                'external_id' => uniqid('MSG'),
                'sender_id' => $booking->user->id,
                'type' => Message::TYPES['BOOKING'],
                'sender_message' => $booking->id,
            ]);

            $conversation->update(['last_message_id' => $message->id]);

            self::notifyOnBookingRequest($booking, $booking->user->details['externalId'], self::USER);
            self::notifyOnBookingRequest($booking, $booking->vendor->details['externalId'], self::VENDOR);

            logger('### CONVERSATION INITIALIZED ###');

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private static function notifyOnBookingRequest(Booking $booking, string $userExternalId, string $notificationType): void
    {
        $message = match ($notificationType) {
            self::USER => 'Hi there, ' . $booking->vendor->details['firstName'] . ' has been notified about your request. We will notify you once your request is accepted',
            default => 'You have a pending rental request from ' . $booking->user->details['firstName'] . ' to accept.'
        };

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'New Rental',
            'body' => $message,
            'data' => json_encode(new  ConversationResource($booking->message->conversation->load(['user', 'vendor', 'messages']))),
            'notificationAction' => 'chat'
        ]))->sendPush();
    }
}
