<?php

namespace App\Http\Controllers;

use App\Actions\Conversation\SendMessageAction;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\ConversationResource;
use App\Models\conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function show(conversation $conversation): JsonResponse
    {
        return successfulJsonResponse(
            new ConversationResource(
                $conversation->load(['messages.bookingData', 'user', 'vendor'])
            )
        );
    }

    public function sendMessage(Request $request, MessageRequest $messageRequest, Conversation $conversation, SendMessageAction $action): JsonResponse
    {
        return $action->handle($request, $messageRequest, $conversation);
    }

}
