<?php

namespace App\Services;

use App\Account;
use App\Attachment;
use App\Events\PendingTransactionCreated;
use App\Events\TransactionCreated;
use App\Http\Requests\CreateTransaction;
use App\PendingTransaction;
use App\Rate;
use App\Setting;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CreateTransactionService
{
    /**
     * @param CreateTransaction $request
     * @return ResponseFactory|Response|void
     */
    public function make(CreateTransaction $request)
    {
        $venezuelan_account = Account::find($request->venezuelan_operator_account_id);
        $foreign_account = Account::find($request->operator_account_id);

        $currency = $foreign_account->bank->currency;
        $actualRate=$this->actualRate($currency->id);
        $isCoordinator=$request->user()->hasRole('coordinator');
        $amountInBs = $actualRate * $request->amount;
        $attachmentIds=$request->received_transaction_attachment_ids;

        if ($amountInBs > $venezuelan_account->balance) {
            return abort(424, 'No hay dinero disponible suficiente en la cuenta seleccionada');
        }
        if ($actualRate !== $request->rate && !$isCoordinator) {
             $this->createPendingTransaction($request);
             return response('Request to Coordinator Made', 200);
        }

        $incomeTransaction = $this->createIncomeTransaction($request);
        $this->assignAttachments($attachmentIds,$incomeTransaction);
        $this->venezuelanOperatorAssignedTransaction($request, $incomeTransaction, $amountInBs);
        $this->taxTransaction($amountInBs, $request, $incomeTransaction);
        broadcast(new TransactionCreated($request->user(), 'made transaction'))->toOthers();

        return response('All transactions created', 201);
    }

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
     * @param $pendingTransaction
     */
    private function assignAttachments(array $attachmentsIds, $pendingTransaction): void
    {
        foreach ($attachmentsIds as $attachmentId) {
            $attachment = Attachment::find($attachmentId);
            $pendingTransaction->attachments()->save($attachment);
        }
    }

    /**
     * @param CreateTransaction $request
     * @return Transaction
     */
    private function createIncomeTransaction(CreateTransaction $request): Transaction
    {
        $incomeTransactionData = [
            'client_id' => $request->client_id,
            'from_user_id' => $request->client_id,
            'to_account_id' => $request->operator_account_id,
            'transaction_number' => $request->transaction_number,
            'to_user_id' => $request->user()->id,
            'amount' => $request->amount,
            'status' => 'confirmed',
            'type' => 'income',
        ];
        return Transaction::create($incomeTransactionData);
    }

    /**
     * @param CreateTransaction $request
     * @param $incomeTransaction
     * @param $amountInBs
     * @return Transaction
     */
    private function venezuelanOperatorAssignedTransaction(CreateTransaction $request, $incomeTransaction, $amountInBs): Transaction
    {
        $venezuelanOperatorAssignedTransaction = [
            'client_id' => $request->client_id,
            'related_transaction_id' => $incomeTransaction->id,
            'from_user_id' => $request->venezuelan_operator_id,
            'from_account_id' => $request->venezuelan_operator_account_id,
            'to_user_id' => $request->receiver_id,
            'to_account_id' => $request->receiver_account_id,
            'amount' => $amountInBs,
            'status' => 'assigned',
            'type' => 'outcome',
        ];
        return Transaction::create($venezuelanOperatorAssignedTransaction);
    }

    /**
     * @param $amountInBs
     * @param CreateTransaction $request
     * @param $incomeTransaction
     * @return Transaction
     */
    private function taxTransaction($amountInBs, CreateTransaction $request, $incomeTransaction): Transaction
    {
        $set = Setting::where('key', 'venezuelanBankTax')->first();
        $taxVal = (float)str_replace(',', '.', $set->value);
        if ($taxVal !== 0) {
            $amountTax = $amountInBs * $taxVal / 100;
            $venezuelanTax = [
                'client_id' => $request->client_id,
                'related_transaction_id' => $incomeTransaction->id,
                'from_account_id' => null,
                'from_user_id' => null,
                'to_user_id' => $request->venezuelan_operator_id,
                'to_account_id' => $request->venezuelan_operator_account_id,
                'amount' => $amountTax,
                'status' => 'terminated',
                'type' => 'outcome',
            ];
            return Transaction::create($venezuelanTax);
        }
    }
}
