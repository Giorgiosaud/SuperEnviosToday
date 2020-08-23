<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Currency;
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
    //
}
