<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $client;
    public $isUpdate;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Client $client, $isUpdate = true)
    {
        $this->client = $client;
        /* we need is update flag to determine if modal is being created or udpated */
        $this->isUpdate = $isUpdate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
