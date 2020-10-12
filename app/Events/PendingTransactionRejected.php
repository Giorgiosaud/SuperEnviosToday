<?php

namespace App\Events;

use App\Models\PendingTransaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PendingTransactionRejected
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $pendingTransaction;

  /**
   * Create a new event instance.
   *
   * @param PendingTransaction $pendingTransaction
   */
  public function __construct(PendingTransaction $pendingTransaction)
  {
    $this->pendingTransaction = $pendingTransaction;
    //
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('pending-transaction-rejected');
  }
}
