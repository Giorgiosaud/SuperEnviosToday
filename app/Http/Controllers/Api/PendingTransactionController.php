<?php

namespace App\Http\Controllers\Api;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PendingTransaction $pendingTransaction)
    {
        return $request->user();
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
    protected function acceptTransaction(PendingTransaction $pendingTransaction,User $user, CreateTransactionService $createTransactionService){
        $values = $pendingTransaction->toArray();
        $transactionRequest = new CreateTransaction($values);
        //$transactionRequest->setUserResolver($request->getUserResolver());
        $createTransactionService->make($transactionRequest);
        $pendingTransaction->status = 'aprooved';
        $pendingTransaction->save();

        return $pendingTransaction;


        return $pendingTransaction;
    }
    protected function rejectTransaction(PendingTransaction $pendingTransaction){
        $pendingTransaction->status = 'rejected';
        $pendingTransaction->save();
        broadcast(new PendingTransactionRejected($pendingTransaction));

        return $pendingTransaction;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
