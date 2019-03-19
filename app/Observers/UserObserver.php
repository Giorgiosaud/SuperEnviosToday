<?php

namespace App\Observers;

use App\Account;
use App\Bank;
use App\Currency;
use App\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param \App\User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        return $user->setRole('client');
    }

    /**
     * Handle the user "updated" event.
     *
     * @param \App\User $user
     *
     * @return void
     */
    public function updated(User $user)
    {

        if($user->hasRole('venezuelan_operator')){
            $currency=Currency::whereName('Bolivar Soberano')->first();
            $bank=Bank::whereName('Efectivo')->whereCurrencyId($currency->id)->first();
            factory(\App\Account::class)->create([
                'bank_id'=>$bank->id,
                'user_id' => $user->id,
                'is_operator_account' => true,
            ]);
        }
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param \App\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param \App\User $user
     *
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param \App\User $user
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
