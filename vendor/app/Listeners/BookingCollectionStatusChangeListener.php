<?php

namespace App\Listeners;

use App\Custom\BookingStatus;
use App\Events\BookingCollectionStatusChangeEvent;
use App\Http\Resources\ConversationResource;
use App\Http\Services\NotificationService;
use App\Models\Booking;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BookingCollectionStatusChangeListener
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
    public function handle(BookingCollectionStatusChangeEvent $event): void
    {
        $booking = $event->booking;

        $user = $booking->user;
        $vendor = $booking->vendor;

        $this->initiateMessage($booking);

        $this->notifyOnCollectionStatusChange($booking, $user->external_id, 'sender');
        $this->notifyOnCollectionStatusChange($booking, $vendor->external_id, 'receiver');
    }

    private function initiateMessage(Booking $booking): void
    {
        logger('### BOOKING COLLECTION STATUS CHANGE EVENT TRIGGERED ###');

        try {
            $user = $booking->user;
//            $vendor = $booking->vendor;

            $message = match ($booking->collection_status) {
                BookingStatus::SUCCESS => [
                    'sender' => "You made a successful payment of ₦ $booking->collection_amount",
                    'receiver' => $user->details['firstName'] . ' has made payment, get ready for pickup'
                ],
                default => null,
            };

            if (!is_null($message)) {
                Message::query()->create([
                    'conversation_id' => $booking->message->conversation_id,
                    'sender_id' => $user->id,
                    'type' => Message::TYPES['TEXT'],
                    'sender_message' => $message['sender'],
                    'receiver_message' => trim($message['receiver'])
                ]);
            }
        } catch (Exception $exception) {
            report($exception);
        }
    }

    private function notifyOnCollectionStatusChange(Booking $booking, string $userExternalId, string $notificationType): void
    {
        $user = $booking->user;

        $message = match ($booking->collection_status) {
            BookingStatus::SUCCESS => [
                'sender' => "Your payment of ₦ $booking->collection_amount was successful. Get ready for pickup.",
                'receiver' => $user->details['firstName'] . ' has made payment, get ready for pickup.'
            ],
            BookingStatus::FAILED, BookingStatus::ABANDONED => [
                'sender' => "Your payment of ₦ $booking->collection_amount was not successful. You can try payment again.",
                'receiver' => null
            ],
            default => null,
        };

        if (isset($message["$notificationType"])) {
            (new NotificationService([
                'userExternalId' => $userExternalId,
                'message' => $message["$notificationType"]
            ]))->sendSms();

            (new NotificationService([
                'userExternalId' => $userExternalId,
                'title' => 'Rental Payment',
                'body' => $message["$notificationType"],
                'data' => json_encode(new  ConversationResource($booking->message->conversation->load(['user', 'vendor', 'messages']))),
                'notificationAction' => 'chat'
            ]))->sendPush();
        }
    }
}
