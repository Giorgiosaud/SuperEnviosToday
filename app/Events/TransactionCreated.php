<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $message;
  public $user;

  /**
   * Create a new event instance.
   *
   * @param User $user
   * @param $message
   */
  public function __construct(User $user, $message)
  {
    //
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('transaction-assigned');
  }
}
