<?php

namespace App\Http\Controllers\Api;

use App\Events\PendingTransactionAccepted;
use App\Events\PendingTransactionRejected;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransaction;
use App\PendingTransaction;
use App\Services\CreateTransactionService;
use App\User;
use Illuminate\Http\Request;

class PendingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index()
    {
        request()->validate([
            'amount'=>'numeric',
            'rate'=>'numeric',
            'venezuelan_bank_to'=>'string'
        ]);
        $pendingTransactions = PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator','foreignAccount.bank.currency','receiverAccount.bank','localOperatorAccount.bank']);
        $moneyFilters = ['amount','rate'];
        foreach ($moneyFilters as $moneyFilter) {
            if (request()->has($moneyFilter)) {
                $pendingTransactions->where($moneyFilter, '=',  request()->get($moneyFilter) *10000);
            }
        }
        if (request()->has('status')) {
            $pendingTransactions->where('status', '=',  request()->status);
        }
        $perPage = request()->has('perPage') ? request()->get('perPage') : config('app.paginated_by');

        return $pendingTransactions
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param PendingTransaction $pendingTransaction
     * @return PendingTransaction|\Illuminate\Http\Response
     */
    public function update(Request $request, PendingTransaction $pendingTransaction)
    {
        $validation=$request->validate([
            'accept_transaction'=>'boolean'
        ]);
        $values=$pendingTransaction->toArray();
        $values['received_transaction_attachment_ids']=$pendingTransaction->attachments->pluck('id')->toArray();
        $transactionRequest=new CreateTransaction($values);
        $transactionRequest->setUserResolver($request->getUserResolver());
        if($validation['accept_transaction']){
            return $this->acceptTransaction($transactionRequest,$pendingTransaction);
        }
        return $this->rejectTransaction($pendingTransaction);
        //
    }

    /**
     * @param CreateTransaction $request
     * @param PendingTransaction $pendingTransaction
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|void
     */
    protected function acceptTransaction(CreateTransaction $request,PendingTransaction $pendingTransaction){
        $createTransactionService=new CreateTransactionService();
        $createTransactionService->make($request);
        $pendingTransaction->status = 'approved';
        $pendingTransaction->save();
        broadcast(new PendingTransactionAccepted($pendingTransaction));
        return $pendingTransaction;
    }

    /**
     * @param PendingTransaction $pendingTransaction
     * @return PendingTransaction
     */
    protected function rejectTransaction(PendingTransaction $pendingTransaction){
        $pendingTransaction->status = 'rejected';
        $pendingTransaction->save();
        broadcast(new PendingTransactionRejected($pendingTransaction));

        return $pendingTransaction;
    }
}
