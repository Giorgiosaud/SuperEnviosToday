<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Mail\TransactionCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendCreatedTransactionNotification implements ShouldQueue
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
     * @param  TransactionCreated  $event
     * @return void
     */
    public function handle(TransactionCreated $event)
    {
      $foreignTransaction = $event->foreignTransaction;
      $venezuelanTransaction = $event->venezuelanTransaction;
      $client = $event->user;
      Mail::to($client)
        ->send(new TransactionCreatedMail($client, $foreignTransaction, $venezuelanTransaction));
        //
    }
}
