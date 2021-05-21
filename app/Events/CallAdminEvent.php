<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallAdminEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data = null;
    public $id;
    /**
     * Create a new event instance.
     *
     * @return void
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
        return new PrivateChannel(env('SOCKET_PREFIX').':Monit.'.$this->id);
    }

    public function broadcastAs(){
        return 'incoming-call';
    }
}
