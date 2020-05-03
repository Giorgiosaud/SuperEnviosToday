<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        return $user->setRole('client');
    }
}
