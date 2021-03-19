<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class CallAdminEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data = null;
    public $id;

    /**
     * Create a new event instance.
     *
     * @param int $id
     * @param null $data
     */
    public function __construct($id = 0, $data = null)
    {
        $this->data = $data;
        $this->id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::info("Id", ['id' => $this->id]);
        return new PrivateChannel(env('SOCKET_PREFIX').':Monit.'.$this->id);
    }

    public function broadcastAs(){
        return 'incoming-call';
    }
}
