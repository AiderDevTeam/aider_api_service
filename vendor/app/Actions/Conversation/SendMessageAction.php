<?php

namespace App\Actions\Conversation;

use App\Http\Requests\MessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Http\Services\NotificationService;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendMessageAction
{
    public function handle(Request $request, MessageRequest $messageRequest, Conversation $conversation): JsonResponse
    {
        logger('### SENDING MESSAGE ###');
        logger($payload = $messageRequest->validated());
        try {
            $user = User::authUser($request->user);

            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'type' => Message::TYPES['NUDGE'],
                'sender_message' => "You sent a nudge 👋 to {$conversation->vendor->details['firstName']}",
                'receiver_message' => $payload['message'],
            ]);

            $this->notifyOnNudge($conversation, $message);

            return successfulJsonResponse(new MessageResource($message));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function notifyOnNudge(Conversation $conversation, Model $message): void
    {
        $user = $conversation->user;
        $vendor = $conversation->vendor;

        (new NotificationService([
            'userExternalId' => $vendor->external_id,
            'message' => $user->details['firstName'] . ' sent you a nudge as a reminder for their rental request.'
        ]))->sendSms();

        (new NotificationService([
            'userExternalId' => $vendor->external_id,
            'title' => 'Nudge From ' . $user->details['firstName'],
            'body' => $message->receiver_message,
            'data' => json_encode(new  ConversationResource($conversation->load(['user', 'vendor', 'messages']))),
            'notificationAction' => 'chat'
        ]))->sendPush();
    }

}
