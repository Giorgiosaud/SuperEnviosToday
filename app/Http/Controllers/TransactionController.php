<?php

namespace App\Http\Controllers;

use App\Account;
use App\Bank;
use App\Currency;
use App\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $currency = Currency::whereId(request()->currency)->first() ?: (Currency::where('identifier', 'CLP')->with('banks.accounts')->first());
        $currencies = Currency::all();
        $banksWithCurrency = Bank::select('id')->where('currency_id',$currency->id)->get();
        $accountsWithCurrencies=Account::select('id')->whereIn('bank_id',$banksWithCurrency->pluck('id'))->get();
        $accountsId=$accountsWithCurrencies->pluck('id');
        $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related'])->whereIn('account_id',$accountsId)->paginate();


        return view('coordinator.transactions.index', compact('transactions', 'currencies', 'currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('operator.transaction.create');
    }
    /**
     * Show the form for adjust transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function adjust()
    {
        return view('coordinator.transactions.adjust');
    }

}
