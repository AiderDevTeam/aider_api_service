<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreRewardValueEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaign;
    public $fullAmount;
    public $fullPoint;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Campaign $campaign, $fullAmount, $fullPoint, $data)
    {
        //
        $this->campaign = $campaign;
        $this->fullAmount = $fullAmount;
        $this->fullPoint = $fullPoint;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
