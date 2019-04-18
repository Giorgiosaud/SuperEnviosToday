<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class VenezuelanTransactionController extends Controller
{
    public function index(){
        $pendingTransactions= Transaction::all();
        return view('operators.transactions-to-do',$pendingTransactions);
    }
    //
}
