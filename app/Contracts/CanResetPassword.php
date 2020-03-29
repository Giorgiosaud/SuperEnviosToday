<?php

namespace App\Contracts;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

interface CanResetPassword extends CanResetPasswordContract
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return array
     */
    public function getDataFromUserForToken();

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token);
}
