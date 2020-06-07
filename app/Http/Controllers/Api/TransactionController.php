<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransaction;
use App\Services\CreateTransactionService;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function verify($transactionNumber){
        $transaction=Transaction::where('transaction_number',$transactionNumber)->with(['client','destinationAccount.bank.currency'])->first();
        if($transaction){
            return response($transaction,200);
        }
        return response('no existe',204);
    }
    public function execute(CreateTransaction $request,CreateTransactionService $createTransactionService){
        return $createTransactionService->make($request);
    }
    //
}
