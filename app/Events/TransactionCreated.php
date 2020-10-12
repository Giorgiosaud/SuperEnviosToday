<?php

namespace App\Events;

use App\Models\Transaction;
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
   * @var Transaction
   */
  public $foreignTransaction;
  /**
   * @var Transaction
   */
  public $venezuelanTransaction;

  /**
   * Create a new event instance.
   *
   * @param User $user
   * @param Transaction $foreignTransaction
   * @param Transaction $venezuelanTransaction
   * @param $message
   */
  public function __construct(User $user, Transaction  $foreignTransaction, Transaction $venezuelanTransaction,$message)
  {
    //
    $this->user = $user;
    $this->foreignTransaction = $foreignTransaction;
    $this->venezuelanTransaction = $venezuelanTransaction;
    $this->message = $message;
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
