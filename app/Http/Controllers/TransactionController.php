<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $transactions=Transaction::with(['operator','client','account.bank.currency','relatedTransactions'])->paginate();
        return view('coordinator.transactions.index',compact('transactions'));
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('operator.transaction.create');
        //
    }

}
