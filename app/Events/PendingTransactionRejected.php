<?php

namespace App\Events;

use App\PendingTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendingTransactionRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    protected $pendingTransaction;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PendingTransaction $pendingTransaction)
    {
        $this->pendingTransaction = $pendingTransaction;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('transaction-rejected');
    }
}
