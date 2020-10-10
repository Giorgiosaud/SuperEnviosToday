<?php

namespace App\Events;

use App\Models\Bank;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestoredBank
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var Bank
   */
  public $bank;

  /**
   * Create a new event instance.
   *
   * @return void
   */
  public function __construct(Bank $bank)
  {
    $this->bank = $bank;
    //
  }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('channel-name');
  }
}
