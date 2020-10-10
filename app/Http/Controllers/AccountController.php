<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Currency;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        $accounts = Account::whereIsOperator(true)
            ->with('bank.currency')
            ->with('owners')
            ->paginate();
        $accounts->append(['balance']);

        $currencies = Currency::all();
        return view('coordinator.accounts.index', compact('accounts', 'banks', 'currencies'));
    }
    public function show(Account $account){
      $account->bank->currency;
      $account->append('balance');
      return view('coordinator.accounts.show',compact('account'));
    }
    //
}
