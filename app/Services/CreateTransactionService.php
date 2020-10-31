<?php

namespace App\Services;

use App\Events\PendingTransactionCreated;
use App\Events\TransactionCreated;
use App\Http\Requests\CreateTransaction;
use App\Models\Account;
use App\Models\Attachment;
use App\Models\PendingTransaction;
use App\Models\Rate;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{
  /**
   * @param CreateTransaction $request
   * @return Application|ResponseFactory|Response|void
   */
  public function make(CreateTransaction $request)
  {
    $transaction = DB::transaction(function () use ($request) {
      $venezuelan_account = Account::find($request->venezuelan_operator_account_id);
      $foreign_account = Account::find($request->operator_account_id);

      $currency = $foreign_account->bank->currency;
      $actualRate = $this->actualRate($currency->id);
      $isCoordinator = $request->user()->hasRole('coordinator');
      $amountInBs = $request->rate * $request->amount;
      $attachmentIds = $request->received_transaction_attachment_ids;
      if ($amountInBs > $venezuelan_account->balance) {
        return abort(424, 'No hay dinero disponible suficiente en la cuenta seleccionada');
      }
      $request->merge(['track_number' => uniqid()]);

      if ($actualRate !== $request->rate && !$isCoordinator) {
        $this->createPendingTransaction($request);
        return response('Request to Coordinator Made', 200);
      }

      $incomeTransaction = $this->createIncomeTransaction($request);
      $this->assignAttachments($attachmentIds, $incomeTransaction);
      $outcomeOfVenezuelanAccount = $this->extractMoneyFromVenezuelanAccount($request, $amountInBs);
      $venezuelanAssigned = $this->venezuelanOperatorAssignedTransaction($request, $amountInBs);
      $taxTransaction = $this->taxTransaction($request, $amountInBs);
      return compact('incomeTransaction', 'outcomeOfVenezuelanAccount', 'venezuelanAssigned', 'taxTransaction');
    });
    broadcast(new TransactionCreated(User::find($request->client_id), $transaction['incomeTransaction'], $transaction['venezuelanAssigned'], 'made transaction'))->toOthers();

    return response('All transactions created', 201);
  }

  /**
   * @param $currencyId
   * @return mixed
   */
  private function actualRate($currencyId)
  {
    $rate = Rate::whereCurrencyId($currencyId)->where('since', '<=', Carbon::now())->orderBy('since', 'DESC')->first();
    return $rate->amount;
  }

  /**
   * @param CreateTransaction $request
   * @return mixed
   */
  private function createPendingTransaction(CreateTransaction $request)
  {
    $pendingTransaction = $request->all();
    $pendingTransaction['operator_id'] = $request->user()->id;
    $pendingTransaction = PendingTransaction::create($pendingTransaction);
    $this->assignAttachments($request->received_transaction_attachment_ids, $pendingTransaction);
    broadcast(new PendingTransactionCreated($request->user()))->toOthers();
    return $pendingTransaction;
  }

  /**
   * @param array $attachmentsIds
   * @param $Transaction
   */
  private function assignAttachments(array $attachmentsIds, $Transaction): void
  {
    foreach ($attachmentsIds as $attachmentId) {
      $attachment = Attachment::find($attachmentId);
      $Transaction->attachments()->save($attachment);
    }
  }

  /**
   * @param CreateTransaction $request
   * @return Transaction
   */
  private function createIncomeTransaction(CreateTransaction $request): Transaction
  {
    $incomeTransactionData = [
      'account_id' => $request->operator_account_id,
      'client_id' => $request->client_id,
      'operator_id' => $request->user()->id,
      'track_number' => $request->track_number,
      'bank_reference' => $request->transaction_number,
      'amount' => $request->amount,
      'status' => 'executed',
    ];
    return Transaction::create($incomeTransactionData);
  }

  /**
   * @param CreateTransaction $request
   * @param int $amountInBs
   * @return Transaction
   */
  private function extractMoneyFromVenezuelanAccount(CreateTransaction $request, int $amountInBs)
  {
    $extractMoneyFromVenezuelanAccount = [
      'account_id' => $request->venezuelan_operator_account_id,
      'client_id' => null,
      'operator_id' => $request->venezuelan_operator_id,
      'track_number' => $request->track_number,
      'bank_reference' => null,
      'amount' => -1 * $amountInBs,
      'status' => 'pending',
      'comment' => null
    ];
    return Transaction::create($extractMoneyFromVenezuelanAccount);
  }

  /**
   * @param CreateTransaction $request
   * @param $amountInBs
   * @return Transaction
   */
  private function venezuelanOperatorAssignedTransaction(CreateTransaction $request, $amountInBs): Transaction
  {
    $venezuelanOperatorAssignedTransaction = [
      'account_id' => $request->receiver_account_id,
      'client_id' => $request->receiver_id,
      'operator_id' => $request->venezuelan_operator_id,
      'track_number' => $request->track_number,
      'bank_reference' => null,
      'amount' => $amountInBs,
      'status' => 'pending',
      'comment' => null
    ];
    return Transaction::create($venezuelanOperatorAssignedTransaction);
  }

  /**
   * @param CreateTransaction $request
   * @param $amountInBs
   * @return Transaction|null
   */
  private function taxTransaction(CreateTransaction $request, $amountInBs): ?Transaction
  {
    $set = Setting::where('key', 'venezuelanBankTax')->first();
    $taxVal = (float)str_replace(',', '.', $set->value);
    if (intval(abs($taxVal)) !== 0) {
      $amountTax = $amountInBs * $taxVal / 100;
      $venezuelanTax = [
        'account_id' => $request->venezuelan_operator_account_id,
        'client_id' => null,
        'operator_id' => $request->venezuelan_operator_id,
        'track_number' => $request->track_number,
        'bank_reference' => null,
        'amount' => -1 * $amountTax,
        'status' => 'pending',
        'comment' => 'tax'
      ];
      return Transaction::create($venezuelanTax);
    }
    return null;
  }


}
