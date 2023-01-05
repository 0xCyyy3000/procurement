<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Requisition implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id, $name, $context, $evaluator = [], $event;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $name, $context, $evaluator, $event)
    {
        $this->id = $id;
        $this->name = $name;
        $this->context = $context;
        $this->evaluator = $evaluator;
        $this->event = $event;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('requisition-channel');
    }

    public function broadcastAs()
    {
        return 'requisition-event';
    }
}
