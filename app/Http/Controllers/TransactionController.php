<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
  /**
   * @return Application|Factory|View
   */
  public function index()
  {
    $currency = Currency::whereId(request()->currency)->first() ?: (Currency::where('identifier', 'CLP')->with('banks.accounts')->first());
    $currencies = Currency::all();
    $banksWithCurrency = Bank::select('id')->where('currency_id', $currency->id)->get();
    $accountsWithCurrencies = Account::select('id')->whereIn('bank_id', $banksWithCurrency->pluck('id'))->whereIsOperator(true)->get();
    $accountsId = $accountsWithCurrencies->pluck('id');
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related.operator', 'related.client', 'related.account.bank.currency'])->whereIn('account_id', $accountsId)->paginate();


    return view('coordinator.transactions.index', compact('transactions', 'currencies', 'currency'));
  }

  /**
   * @return Application|Factory|View
   */
  public function myIndex()
  {
    $currency = Currency::whereId(request()->currency)->first() ?: (Currency::where('identifier', 'CLP')->first());
    $currencies = Currency::all();
    $user = request()->user();
    $accountsId = $currency->accounts()->whereIn('accounts.id', $user->accounts->pluck('id'))->get()->pluck('id');
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related.operator', 'related.client', 'related.account.bank.currency'])
      ->whereIn('account_id', $accountsId)->paginate();
    return view('operator.transactions.index', compact('transactions', 'currencies', 'currency', 'user'));
  }

  public function venezuelanIndex()
  {
    $currency = Currency::where('identifier', 'BsS')->first();
    $user = request()->user();
    $accountsId = $currency->accounts()->whereIn('accounts.id', $user->accounts->pluck('id'))->get()->pluck('id');
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'attachments'])
      ->whereIn('account_id', $accountsId)->paginate();
    return view('operator.transactions.venezuelan.my-index', compact('transactions', 'currency'));
  }



  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    return view('operator.transaction.create');
  }
}
