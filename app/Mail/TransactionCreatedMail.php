<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TransactionCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

  /**
   * @var User
   */
  private $client;
  /**
   * @var Transaction
   */
  private $foreignTransaction;
  /**
   * @var Transaction
   */
  private $venezuelanTransaction;

  /**
   * Create a new message instance.
   *
   * @param User $client
   * @param Transaction $foreignTransaction
   * @param Transaction $venezuelanTransaction
   */
    public function __construct(User $client, Transaction $foreignTransaction, Transaction $venezuelanTransaction)
    {
        //
      $this->client = $client;
      $this->foreignTransaction = $foreignTransaction;
      $this->venezuelanTransaction = $venezuelanTransaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $this->venezuelanTransaction->client;
      $this->venezuelanTransaction->account;
      $this->foreignTransaction->account->bank->currency;
      Carbon::setLocale('es');
      $fecha = Carbon::parse($this->foreignTransaction->created_at);
      $humanTime=$fecha->diffForHumans(); //esto se mostrará en español
      return $this->markdown('emails.transaction.created')
        ->subject(__('email.TRANSACTION:CREATED:SUBJECT',
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
