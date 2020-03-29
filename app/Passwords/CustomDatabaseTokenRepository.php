<?php

namespace App\Passwords;

use App\Contracts\CanResetPassword;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

class CustomDatabaseTokenRepository extends DatabaseTokenRepository
{
    /**
     * Create a new token record.
     *
     * @param CanResetPassword $user
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $userData = $user->getDataFromUserForToken();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($userData, $token));

        return $token;
    }

    /**
     * Build the record payload for the table.
     *
     * @param array  $userData
     * @param string $token
     *
     * @return array
     */
    protected function getPayload($userData, $token)
    {
        return ['idn' => $userData['idn'], 'idn_type' => $userData['idn_type'], 'token' => $this->hasher->make($token), 'created_at' => new Carbon()];
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param CanResetPasswordContract $user
     *
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where($user->getDataFromUserForToken())->delete();
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                      $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
         $user->getDataFromUserForToken()
      )->first();

        return $record &&
        !$this->tokenExpired($record['created_at']) &&
        $this->hasher->check($token, $record['token']);
    }
}
