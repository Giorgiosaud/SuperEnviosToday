<?php


namespace App\Auth;


use Exception;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

class DatabaseTokenRepository extends \Illuminate\Auth\Passwords\DatabaseTokenRepository
{
  /**
   * Create a new token record.
   *
   * @param CanResetPasswordContract $user
   * @return string
   * @throws Exception
   */
  public function create(CanResetPasswordContract $user)
  {

    $this->deleteExisting($user);
    // We will create a new, random token for the user so that we can e-mail them
    // a safe link to the password reset form. Then we will insert a record in
    // the database so that we can verify the token within the actual reset.
    $token = $this->createNewToken();
    $this->getTable()->insert($this->getPayload($user, $token));
    return $token;
  }

  /**
   * Build the record payload for the table.
   *
   * @param $user
   * @param string $token
   * @return array
   * @throws Exception
   */
  protected function getPayload($user, $token)
  {
    return ['email' => $user->email, 'idn' => $user->idn, 'idn_type' => $user->idn_type, 'token' => $this->hasher->make($token), 'created_at' => new Carbon];
  }

}
