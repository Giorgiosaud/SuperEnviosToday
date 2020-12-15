<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TransactionExecutedMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * @var Transaction
   */
  private $foreignTransaction;
  /**
   * @var User
   */
  private $client;
  /**
   * @var Transaction
   */
  private $venezuelanTransaction;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(User $client, Transaction $foreignTransaction, Transaction $venezuelanTransaction)
  {
    //
    $this->foreignTransaction = $foreignTransaction;
    $this->client = $client;
    $this->venezuelanTransaction = $venezuelanTransaction;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $this->attachments = $this->venezuelanTransaction
      ->attachments
      ->pluck('path')
      ->map(function ($path) {
        return ['file'=>public_path() . $path,'options'=>[]];
      })->toArray();
    Carbon::setLocale('es');
    $fecha = Carbon::parse($this->venezuelanTransaction->updated_at);
    $humanTime=$fecha->diffForHumans(); //esto se mostrará en español
    return $this->markdown('emails.transaction.executed')
      ->subject(__('email.TRANSACTION:COMPLETED:SUBJECT',
          ['cliente' => $this->client->name . ' ' . $this->client->last_name,
            'human_time'=>$humanTime]
        )
      )
      ->with([
        'client' => $this->client,
        'foreignTransaction' => $this->foreignTransaction,
        'venezuelanTransaction' => $this->venezuelanTransaction
      ]);
  }
}
