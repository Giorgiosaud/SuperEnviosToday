<?php

namespace App\Listeners;

use App\Events\TransactionExecuted;
use App\Mail\TransactionExecutedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCompletedTransactionNotification implements ShouldQueue
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   *
   * @param TransactionExecuted $event
   * @return void
   */
  public function handle(TransactionExecuted $event)
  {
    $transactions = $event->transactions;
    $foreign = $transactions->first(function ($transaction) {
      return $transaction->account->bank->currency->identifier != 'BsS';
    });
    $client = $foreign->client;
    $venezuelan = $transactions->first(function ($transaction) {
      return $transaction->account->bank->currency->identifier == 'BsS' && $transaction->amount > 0;
    });
    Mail::to($client)
      ->send(new TransactionExecutedMail($client, $foreign, $venezuelan));
  }
}
