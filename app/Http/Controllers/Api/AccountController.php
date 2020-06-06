<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Currency;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;


class AccountController extends Controller
{
    public function getAccounts($currencyId)
    {
        $banks = Bank::select('id')->where('currency_id', $currencyId)->pluck('id');
        return Account::with('bank')
            ->whereIn('bank_id', $banks)
            ->whereHas('owners', function ($q) {
                return $q->where('user_id', auth()->user()->id);
            })
            ->where('is_operator', true)
            ->get();
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function save(User $user, Request $request)
    {
        $data=$request->validate([
            'bank_id'=>['required'],
            'number'=>['required','numeric'],
            'type'=>[]
        ]);
        $account=Account::create($data);
        return $user->accounts()->save($account);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function indexBase(){
        $banks = Bank::select('id')->where('currency_id', config('app.base_currency_id'))->pluck('id');
        return Account::with(['bank','owners'])
            ->whereIn('bank_id', $banks)

            ->where('is_operator', true)
            ->get()
            ->append('balance');
    }
    //
}
