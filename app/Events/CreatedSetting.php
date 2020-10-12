<?php

namespace App\Events;

use App\Models\Setting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatedSetting
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  /**
   * @var Setting
   */
  public $setting;

  /**
   * Create a new event instance.
   *
   * @param Setting $setting
   */
  public function __construct(Setting $setting)
  {
    $this->setting = $setting;
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
