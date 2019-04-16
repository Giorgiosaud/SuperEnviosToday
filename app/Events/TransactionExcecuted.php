<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TransactionExcecuted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $venezuelan_operator;
    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($venezuelan_operator)
    {
        $this->venezuelan_operator= $venezuelan_operator;
        $this->message  = "{$venezuelan_operator} tiene una nueva transaccion";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        //return new Channel('transaction-assigned');
        return new PrivateChannel('transaction-assigned');
    }
}
