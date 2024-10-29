<?php

namespace App\Observers;

use App\Models\Conversation;

class ConversationObserver
{
    /**
     * Handle the Conversation "creating" event.
     */
    public function creating(Conversation $conversation): void
    {
        $conversation->external_id = uniqid('CONVO');
    }

    /**
     * Handle the Conversation "created" event.
     */
    public function created(Conversation $conversation): void
    {
        //
    }

    /**
     * Handle the Conversation "updated" event.
     */
    public function updated(Conversation $conversation): void
    {
        if ($conversation->isDirty('is_on_going') && !$conversation->isOnGoing()) {
            logger('### SETTING THIS CONVERSATION\'S ON GOING STATUS TO FALSE ###');
            $conversation->messages()->update(['conversation_on_going' => false]);
        }
    }

    /**
     * Handle the Conversation "deleted" event.
     */
    public function deleted(Conversation $conversation): void
    {
        //
    }

    /**
     * Handle the Conversation "restored" event.
     */
    public function restored(Conversation $conversation): void
    {
        //
    }

    /**
     * Handle the Conversation "force deleted" event.
     */
    public function forceDeleted(Conversation $conversation): void
    {
        //
    }
}
