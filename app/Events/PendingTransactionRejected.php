<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendingTransactionRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pendingTransaction;
    /**
     * Create a new event instance.
     *
     * @param \App\PendingTransaction $pendingTransaction
     */
    public function __construct(\App\PendingTransaction $pendingTransaction)
    {
        $this->pendingTransaction=$pendingTransaction;
        //
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
