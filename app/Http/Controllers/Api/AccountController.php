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
        $accounts = Account::with('bank')
            ->whereIn('bank_id', $banks)
            ->whereHas('owners', function ($q) {
                return $q->where('user_id', auth()->user()->id);
            })
            ->where('is_operator', true)
            ->get();
        return $accounts;
    }

    public function save(User $user,Request $request)
    {
        $data=$request->validate([
            'bank_id'=>['required'],
            'number'=>['required','numeric'],
            'type'=>[]
        ]);
        $account=Account::create($data);
        return $user->accounts()->save($account);
        return Account::create($data);
    }
    public function indexBase(){
        $currency=Currency::find(config('app.base_currency_id'));
        $banksId=$currency->banks->pluck('id')->toArray();
        return $currency->banks()->whereHas('accounts',function($q){
          $q->where('is_operator',true);
        })->get();
            //Account::whereIn('bank_id',$banksId)->where('is_operator',true)->get();
    }
    //
}
