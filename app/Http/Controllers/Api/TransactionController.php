<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function verify($transactionNumber){
        $transaction=Transaction::where('transaction_number',$transactionNumber)->first();
        if($transaction){
            return response($transaction,200);
        }
        return response('no existe',204);
    }
    //
}
