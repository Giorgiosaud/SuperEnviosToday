<?php

namespace App\Observers;

use App\Account;
use App\Bank;
use App\Currency;
use App\User;

class CurrencyObserver
{
    public function created(Currency $currency)
    {
        $bank = factory(Bank::class)->create([
            'name'       => 'Efectivo',
            'currency_id'=> $currency->id,
        ]);
        $users = User::whereHas(
            'roles', function ($q) {
                /* @noinspection PhpUndefinedMethodInspection */
                $q->where('name_id', 'coordinator')->orWhere('name_id', 'foreign_operator');
            })->get();
        foreach ($users as $user) {
            factory(Account::class)->create(['bank_id'=>$bank->id, 'user_id'=>$user->id, 'is_operator_account'=>true]);
        }
        //
    }

    //
}
