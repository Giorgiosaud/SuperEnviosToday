<?php

namespace App\Http\Controllers\Api;

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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        request()->validate([
            'amount'=>'numeric',
            'rate'=>'numeric',
            'venezuelan_bank_to'=>'string'
        ]);
        $pendingTransactions = PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator','foreign_account','receiver_account','operator_account']);
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * TODO Make it work
     */
    public function update(Request $request, PendingTransaction $pendingTransaction)
    {
        $validation=$request->validate([
            'accept_transaction'=>'boolean'
        ]);
        if($validation['accept_transaction']){
            return $request->user();
            return $this->acceptTransaction($pendingTransaction, $request->user());
        }
        return $this->rejectTransaction($pendingTransaction, $request->user());
        //
    }

    /**
     * @param PendingTransaction $pendingTransaction
     * @param User $user
     * @param CreateTransactionService $createTransactionService
     * @return PendingTransaction
     */
    protected function acceptTransaction(PendingTransaction $pendingTransaction, User $user, CreateTransactionService $createTransactionService){
        $values = $pendingTransaction->toArray();
        $transactionRequest = new CreateTransaction($values);
        //$transactionRequest->setUserResolver($request->getUserResolver());
        $createTransactionService->make($transactionRequest);
        $pendingTransaction->status = 'aprooved';
        $pendingTransaction->save();
        return $pendingTransaction;
    }

    /**
     * @param PendingTransaction $pendingTransaction
     * @return PendingTransaction
     */
    protected function rejectTransaction(PendingTransaction $pendingTransaction){
        return $pendingTransaction;
        $pendingTransaction->status = 'rejected';
        $pendingTransaction->save();
        broadcast(new PendingTransactionRejected($pendingTransaction));

        return $pendingTransaction;
    }
}
