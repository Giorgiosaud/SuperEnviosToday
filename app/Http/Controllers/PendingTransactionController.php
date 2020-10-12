<?php

namespace App\Http\Controllers;

use App\Models\PendingTransaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PendingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     */
    public function index()
    {
        $perPage = request()->has('perPage') ? request()->get('perPage') : config('app.paginated_by');
        $pendingTransactions = PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator','foreignAccount.bank.currency','receiverAccount.bank','localOperatorAccount.bank'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return view('coordinator.pendingTransactions.index',compact('pendingTransactions'));
    }
}
