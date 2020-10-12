<?php

namespace App\Events;

use App\Models\Currency;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RestoredCurrency
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var Currency
   */
  public $currency;

  /**
   * Create a new event instance.
   *
   * @param Currency $currency
   */
  public function __construct(Currency $currency)
  {
    $this->currency = $currency;
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
