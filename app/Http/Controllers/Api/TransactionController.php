<?php

namespace App\Http\Controllers\Api;

use App\Account;
use App\Bank;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransaction;
use App\Services\CreateTransactionService;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @param $transactionNumber
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function verify($transactionNumber)
    {
        $transaction = Transaction::where('bank_reference', $transactionNumber)->with(['client', 'account.bank.currency'])->first();
        if ($transaction) {
            return response($transaction, 200);
        }
        return response('no existe', 204);
    }

    /**
     * @param CreateTransaction $request
     * @param CreateTransactionService $createTransactionService
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
     */
    public function execute(CreateTransaction $request, CreateTransactionService $createTransactionService)
    {
        return $createTransactionService->make($request);
    }

    public function index()
    {
        $currency = Currency::whereId(request()->currency)->first();
        $banksWithCurrency = Bank::select('id')->where('currency_id', $currency->id)->get();
        $accountsWithCurrencies = Account::select('id')->whereIn('bank_id', $banksWithCurrency->pluck('id'))->whereIsOperator(true)->get();
        $accountsId = $accountsWithCurrencies->pluck('id');
        $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related'])->whereIn('account_id', $accountsId);
        if (request()->status) {
            $transactions->whereStatus(request()->status);
        }
        return $transactions->paginate();
    }
    //
}
