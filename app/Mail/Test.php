<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Test extends SendgridCategoryBase
{

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $this->addSwiftData('Test-cat','Prueba');

    return $this->markdown('emails.test');
  }

  private function asString($data)
  {
    $json = $this->asJSON($data);

    return wordwrap($json, 76, "\n   ");
  }

  private function asJSON($data)
  {
    $json = json_encode($data);
    $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);

    return $json;
  }
}
