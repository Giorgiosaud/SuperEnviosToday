<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class SendgridCategoryBase extends Mailable
{
  use Queueable, SerializesModels;

  public function addSwiftData($category,$variable)
  {
    $headerData = [
      'category' => $category,
      'unique_args' => [
        'variable_1' => $variable
      ]
    ];

    $header = $this->asString($headerData);

    $this->withSwiftMessage(function ($message) use ($header) {
      $message->getHeaders()
        ->addTextHeader('X-SMTPAPI', $header);
    });
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
