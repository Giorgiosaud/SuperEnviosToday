<?php


    namespace App\Services;


    use App\Account;
    use App\Attachment;
    use App\Events\PendingTransactionAwaiting;
    use App\Events\TransactionExecuted;
    use App\Http\Requests\CreateTransaction;
    use App\PendingTransaction;
    use App\Rate;
    use App\Setting;
    use App\Transaction;
    use Carbon\Carbon;

    class CreateTransactionService
    {
        /**
         * @param CreateTransaction $request
         */
        public function make(CreateTransaction $request){
            //TODO verify if a transactoin with the same amount is already made and return confirmation
            $venezuelan_account=Account::find($request->venezuelan_operator_account_id);
            $foreign_account = Account::find($request->foreign_account_id);
            $currency = $foreign_account->bank->currency;
            if (isset($request->rate) && $this->calculateRate($currency->id) !== $request->rate) {
                if ($request->user()->hasRole('coordinator')) {
                    $amountInBs = $request->rate * $request->amount;
                } else {
                    echo $request->rate;
                    $pending=PendingTransaction::create($request->all());
                    broadcast(new PendingTransactionAwaiting($request->user()))->toOthers();
                    return $pending;
                }
            } else {
                $amountInBs = $this->calculateRate($currency->id) * $request->amount;
            }
            if($amountInBs > $venezuelan_account->balance){
                return abort(424,"No hay dinero disponible suficiente en la cuenta seleccionada");
            }
            $incomeTransactionData = [
                'client_id' => $request->client_id,
                'to_account_id' => $request->foreign_account_id,
                'amount' => $request->amount,
                'status' => 'confirmed',
                'type' => 'income',
            ];
            $incomeTransaction = Transaction::create($incomeTransactionData);
            if(isset($request->received_transaction_attachment_ids)) {
                foreach ($request->received_transaction_attachment_ids as $attachmentId){
                    $attachment=Attachment::find($attachmentId);
                    $incomeTransaction->attachments()->save($attachment);
                }
            }
            $assignedTransactionData = [
                'client_id' => $request->client_id,
                'related_transaction_id' => $incomeTransaction->id,
                'from_account_id' => $request->venezuelan_operator_account_id,
                'to_account_id' => $request->receiver_account_id,
                'amount' => $amountInBs,
                'status' => 'assigned',
                'type' => 'outcome',
            ];
            Transaction::create($assignedTransactionData);
            $set = Setting::where('key','venezuelanBankTax')->first();
            $taxVal=(float) str_replace(',','.',$set->value);
            if($taxVal!==0) {
                $amountTax = $amountInBs * $taxVal / 100;
                $venezuelanTax = [
                    'client_id' => $request->client_id,
                    'related_transaction_id' => $incomeTransaction->id,
                    'from_account_id' => null,
                    'to_account_id' => $request->venezuelan_operator_account_id,
                    'amount' => $amountTax,
                    'status' => 'terminated',
                    'type' => 'outcome',
                ];
                Transaction::create($venezuelanTax);
            }
            broadcast(new TransactionExecuted($request->user(),'made transaction'))->toOthers();
            return response('All transactions created',201);

        }
        private function calculateRate($currId)
        {
            $rate = Rate::whereCurrencyId($currId)->where('since', '<=', Carbon::now())->orderBy('since', 'DESC')->first();
            if ($rate) {
                return $rate->amount;
            } else {
                return 0;
            }
        }

    }
