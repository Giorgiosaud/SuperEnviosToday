<?php

  namespace App\Observers;

  use App\Account;
  use App\Bank;
  use App\Currency;
  use App\User;

  class UserObserver {
    /**
     * Handle the user "created" event.
     *
     * @param User $user
     *
     * @return void
     */
    public function created(User $user) {
      return $user->setRole('client');
    }

    /**
     * Handle the user "updated" event.
     *
     * @param User $user
     *
     * @return void
     */
    public function saved(User $user) {
      if ($user->accounts->count() === 0) {
        if ($user->hasRole('venezuelan_operator')) {
          $currency = Currency::whereName('Bolivares Soberanos')->first();
          $bank = Bank::whereName('Efectivo')->whereCurrencyId($currency->id)->first();
          factory(Account::class)->create([
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'is_operator_account' => true,
          ]);
        } elseif ($user->hasRole('coordinator') || $user->hasRole('foreign_operator')) {
          $currencies = Currency::where('name', '!=', 'Bolivares Soberanos')->get();
          foreach ($currencies as $currency) {
            $bank = Bank::whereName('Efectivo')->whereCurrencyId($currency->id)->first();
            factory(Account::class)->create([
              'bank_id' => $bank->id,
              'user_id' => $user->id,
              'is_operator_account' => true,
            ]);
          }
        }
      }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param User $user
     *
     * @return void
     */
    public function deleted(User $user) {
      //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param User $user
     *
     * @return void
     */
    public function restored(User $user) {
      //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param User $user
     *
     * @return void
     */
    public function forceDeleted(User $user) {
      //
    }
  }
