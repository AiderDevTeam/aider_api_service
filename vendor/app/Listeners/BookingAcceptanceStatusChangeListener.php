<?php

namespace App\Listeners;

use App\Custom\BookingStatus;
use App\Events\BookingAcceptanceStatusChangeEvent;
use App\Http\Resources\ConversationResource;
use App\Http\Services\NotificationService;
use App\Models\Booking;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BookingAcceptanceStatusChangeListener
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
    public function handle(BookingAcceptanceStatusChangeEvent $event): void
    {
        logger('### BOOKING ACCEPTANCE STATUS CHANGE EVENT TRIGGERED ###');

        $booking = $event->booking;
        $this->initializeMessaging($booking);
    }

    public function initializeMessaging(Booking $booking): void
    {
        try {
            $user = $booking->user;
            $vendor = $booking->vendor;

            $status = match ($booking->booking_acceptance_status) {
                BookingStatus::ACCEPTED => 'accepted',
                BookingStatus::REJECTED => 'rejected',
                BookingStatus::CANCELED => 'canceled',
                default => null,
            };

            if (!is_null($status)) {

                $message = $this->getMessage($status, $user, $vendor);

                $messageModel = Message::create([
                    'conversation_id' => $booking->message->conversation_id,
                    'sender_id' => $status === BookingStatus::CANCELED ? $user->id : $vendor->id,
                    'type' => Message::TYPES['TEXT'],
                    'sender_message' => $message['sender'],
                    'receiver_message' => trim($message['receiver']),
                    'conversation_on_going' => $status === BookingStatus::ACCEPTED
                ]);

                $userNotificationMessage = $status === BookingStatus::CANCELED ? $messageModel->sender_message : $messageModel->receiver_message;
                $vendorNotificationMessage = in_array($status, [BookingStatus::ACCEPTED, BookingStatus::REJECTED]) ? $messageModel->sender_message : $messageModel->receiver_message;

                self::notifyOnBookingAcceptanceStatusChange($booking, $user->external_id, $userNotificationMessage);
                self::notifyOnBookingAcceptanceStatusChange($booking, $vendor->external_id, $vendorNotificationMessage);
            }

        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function getMessage(string $status, $user, $vendor): array
    {
        $initiatePaymentText = $status === 'accepted' ? 'You can make payment now.' : '';
        return match ($status) {
            BookingStatus::ACCEPTED, BookingStatus::REJECTED => [
                'sender' => "You have $status {$user->details['firstName']}'s request.",
                'receiver' => "{$vendor->details['firstName']} has $status your request. $initiatePaymentText",
            ],
            BookingStatus::CANCELED => [
                'sender' => 'You have canceled your request',
                'receiver' => "{$user->details['firstName']} canceled the request"
            ]
        };
    }

    private static function notifyOnBookingAcceptanceStatusChange(Booking $booking, string $userExternalId, string $message): void
    {
        (new NotificationService([
            'userExternalId' => $userExternalId,
            'message' => $message
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $userExternalId,
            'title' => 'Rental Status Update',
            'body' => $message,
            'data' => json_encode(new  ConversationResource($booking->message->conversation->load(['user', 'vendor', 'messages']))),
            'notificationAction' => 'chat'
        ]))->sendPush();
    }
}
