<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function validate($transactionNumber){
        return Transaction::where('transaction_number',$transactionNumber)->first();
    }
    //
}
