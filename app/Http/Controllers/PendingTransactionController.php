<?php

namespace App\Http\Controllers;

use App\PendingTransaction;

class PendingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->has('perPage') ? request()->get('perPage') : config('app.paginated_by');
        $pendingTransactions = PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator','foreignAccount.bank.currency','receiverAccount.bank','localOperatorAccount.bank'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);;
        return view('coordinator.pendingTransactions.index',compact('pendingTransactions'));
    }
}
