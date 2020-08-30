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

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
      $currency = Currency::whereId(request()->currency)->first();
      $banksWithCurrency = Bank::select('id')->where('currency_id', $currency->id)->get();
      $accountsWithCurrencies = Account::select('id')->whereIn('bank_id', $banksWithCurrency->pluck('id'))->whereIsOperator(true)->get();
      $accountsId = $accountsWithCurrencies->pluck('id');
      $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related.operator', 'related.client', 'related.account.bank.currency'])->whereIn('account_id', $accountsId);
      if (request()->status) {
        $transactions->whereStatus(request()->status);
      }
      return $transactions->paginate();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myIndex()
    {
      $currency = Currency::whereId(request()->currency)->first();
      $banksWithCurrency = Bank::select('id')->where('currency_id', $currency->id)->get();
      $accountsWithCurrencies = Account::select('id')->whereIn('bank_id', $banksWithCurrency->pluck('id'))->whereHas('owners', function ($query) use ($user) {
        $query->where('user_id', $user->id);
      })->get();
      $accountsId = $accountsWithCurrencies->pluck('id');
      $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related.operator', 'related.client', 'related.account.bank.currency'])->whereIn('account_id', $accountsId);
      if (request()->status) {
        $transactions->whereStatus(request()->status);
      }
      return $transactions->paginate();
    }

    public function create(Account $account, Request $request)
    {
      $data = $request->validate([
        'account_id' => ['required', 'numeric', 'exists:accounts,id'],
        'type' => ['required', 'in:income,outcome'],
        'amount' => ['required', 'numeric'],
        'comment' => ['string']
      ]);
      $data['status'] = 'executed';
      $data['operator_id'] = $request->user()->id;
      $data['amount']=$data['type']=='outcome'?-1*$data['amount']:1*$data['amount'];
      return Transaction::create($data);
    }
    //
  }
