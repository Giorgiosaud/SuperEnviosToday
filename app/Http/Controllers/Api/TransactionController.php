<?php

namespace App\Http\Controllers\Api;

use App\Events\TransactionExecuted;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransaction;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Transaction;
use App\Services\CreateTransactionService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class TransactionController extends Controller
{
  /**
   * @param $transactionNumber
   * @return Application|ResponseFactory|Response
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
   * @return Application|ResponseFactory|Response|void
   */
  public function execute(CreateTransaction $request, CreateTransactionService $createTransactionService)
  {
    return $createTransactionService->make($request);
  }

  /**
   * @return LengthAwarePaginator
   */
  public function index()
  {
    $currency = Currency::whereId(request()->currency)->first();
    $banksWithCurrency = Bank::select('id')->where('currency_id', $currency->id)->get();
    $accountsWithCurrencies = Account::select('id')
      ->whereIn('bank_id', $banksWithCurrency
        ->pluck('id'))
      ->where('is_operator',true)
      ->get();
    $accountsId = $accountsWithCurrencies->pluck('id');
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency', 'related.operator', 'related.client', 'related.account.bank.currency'])
      ->whereIn('account_id', $accountsId);
    if (request()->status) {
      $transactions->where('status',request()->status);
    }
    return $transactions->paginate();
  }

  /**
   * @return LengthAwarePaginator
   */
  public function myIndex()
  {
    $currency = Currency::where('id',request()->currency)->first();
    $user = request()->user();
    $accountsId = $currency->accounts()->whereIn('accounts.id', $user->accounts->pluck('id'))->get()->pluck('id');
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency'])
      ->whereIn('account_id', $accountsId);
    return $transactions->paginate();
  }

  /**
   * @return LengthAwarePaginator
   */
  public function myVenezuelanIndex()
  {
    $currency = Currency::where('identifier', 'BsS')->first();
    $user = request()->user();
    if(request()->account){
      $accountsId = [request()->account];
    }else {
      $accountsId = $currency->accounts()->whereIn('accounts.id', $user->accounts->pluck('id'))->get()->pluck('id');
    }
    $transactions = Transaction::with(['operator', 'client', 'account.bank.currency','attachments','foreignRelated'])
      ->whereIn('account_id', $accountsId);
    if (request()->status) {
      $transactions->where('status',request()->status);
    }
    return $transactions->paginate();
  }

  /**
   * @param Account $account
   * @param Request $request
   * @return mixed
   */
  public function create(Account $account, Request $request)
  {
    $data = $request->validate([
      'type' => ['required', 'in:income,outcome'],
      'amount' => ['required', 'numeric'],
    ]);
    $data['status'] = 'executed';
    $data['operator_id'] = $request->user()->id;
    $data['amount'] = $data['type'] == 'outcome' ? -1 * $data['amount'] : 1 * $data['amount'];
    $data['comment']=$request->comment;
    return $account->transactions()->save(new Transaction($data));
  }

  public function relatedVenezuelanTransactions(Transaction $transaction)
  {
    $collection = $transaction->related()->with('account.bank.currency', function ($q) {
      $q->where('identifier', 'BsS');
    })
      ->with(['account.owners', 'attachments'])->get();
    return $collection->where('account.bank.currency', '!=', null)
      ->where('amount', '>', 0)->first();
  }
  public function myRelatedTransactions(Transaction $transaction)
  {
    return $transaction->related()->with('account.bank.currency', function ($q) {
      $q->where('identifier', 'BsS');
    })
      ->with(['account.owners', 'attachments','operator'])->get();
  }

  public function updateRelatedVenezuelanTransaction(Request $request)
  {
    $data = $this->validate($request, [
      'transaction.id' => ['required', 'numeric'],
      'transaction.bank_reference' => ['required', 'string'],
      'transaction.attachments.*.id' => ['required', 'numeric'],
    ]);
    //TODO: Validate if ref number exist
    $venezuelanTransaction = Transaction::find($data['transaction']['id']);
    $relatedTransactions=$venezuelanTransaction->related;
    $attachmentsId = Arr::pluck($data['transaction']['attachments'], 'id');
    $attachments = Attachment::whereIn('id', $attachmentsId)->get();
    $bankReference=$data['transaction']['bank_reference'];
    $attachments->each(function ($attachment) use ($relatedTransactions,$bankReference) {
      $this->runInEachAttachment($relatedTransactions,$bankReference, $attachment);
    });
    event(new TransactionExecuted($relatedTransactions));
    return response('executed', 201);
  }
  //

  /**
   * @param $attachment
   * @param $transaction
   */
  protected function saveAttachmentAndExecuteTransaction($attachment,$bankReference, $transaction): void
  {
    $attachment->transactions()->save($transaction);
    if($transaction->account->bank->currency->identifier==='BsS')
    $transaction['bank_reference']=$bankReference;
    $transaction->status = 'executed';
    $transaction->save();
  }

  /**
   * @param $relatedTransactions
   * @param $attachment
   */
  protected function runInEachAttachment($relatedTransactions,$bankReference, $attachment): void
  {
    $relatedTransactions->each(function ($transaction) use ($attachment,$bankReference) {
      $this->saveAttachmentAndExecuteTransaction($attachment,$bankReference, $transaction);
    });
  }
}
