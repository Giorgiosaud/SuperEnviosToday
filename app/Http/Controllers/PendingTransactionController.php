<?php

namespace App\Http\Controllers;

use App\Events\PendingTransactionRejected;
use App\Http\Requests\CreateTransaction;
use App\PendingTransaction;
use App\Services\CreateTransactionService;
use Illuminate\Http\Request;

class PendingTransactionController extends Controller
{
    public function index()
    {
        return view('coordinator.pendingTransactions');
    }

    public function indexAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 10;
        $q = $request->has('q') ? $request->get('q') : null;
        if ($q) {
            return PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator'])
                ->whereHas('client', function ($query) use ($q) {
                    return $query->where('name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%')
                        ->orWhere('idn', 'like', '%'.$q.'%')
                        ->orWhere('idn_type', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                })
                ->orWhereHas('receiver_account.bank', function ($query) use ($q) {
                    return $query->where('name', 'like', '%'.$q.'%');
                })
                ->orWhereHas('foreignOperator', function ($query) use ($q) {
                    return $query->where('name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                })
                ->orWhereHas('operator_account.bank', function ($query) use ($q) {
                    return $query->where('name', 'like', '%'.$q.'%');
                })
                ->orWhere('id', 'like', '%'.$q.'%')                
                ->orderBy('created_at', 'desc')

                ->paginate($limit);
        }

        return PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator', 'receiver_account', 'operator_account'])
        ->orderBy('created_at', 'desc')
        ->paginate($limit);
    }

    public function myTransactions()
    {
        return view('operators.transactions-pending');
    }

    public function myPendingTransactionsAPI(Request $request)
    {
        if ($request->user()->hasRole('coordinator')) {
            return  $this->indexAPI($request);
        } else {
            $limit = $request->has('perPage') ? $request->get('perPage') : 5;
            $q = $request->has('q') ? $request->get('q') : null;
            $foreignUser = $request->user();
            if ($q) {
                return PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator', 'receiver_account', 'operator_account'])
                    ->where('foreign_id', $foreignUser->id)
                    ->whereHas('client', function ($query) use ($q) {
                        return $query->where('name', 'like', '%'.$q.'%')
                            ->orWhere('last_name', 'like', '%'.$q.'%')
                            ->orWhere('idn', 'like', '%'.$q.'%')
                            ->orWhere('idn_type', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%');
                    })
                    ->orWhereHas('receiver_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%'.$q.'%');
                    })
                    ->orWhereHas('operator_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%'.$q.'%');
                    })
                    ->orWhere('id', 'like', '%'.$q.'%')
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit);
            }

            return PendingTransaction::with(['client', 'receiver', 'venezuelanOperator', 'foreignOperator', 'receiver_account', 'operator_account'])
                ->where('foreign_id', $foreignUser->id)
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }
    }

    public function approveAPI(Request $request, PendingTransaction $pendingTransaction, CreateTransactionService $createTransactionService)
    {
        $values = $pendingTransaction->toArray();
        $values['receiver_user_id'] = $pendingTransaction->receiver_id;
        $transactionRequest = new CreateTransaction($values);
        $transactionRequest->setUserResolver($request->getUserResolver());
        $createTransactionService->make($transactionRequest);
        $pendingTransaction->status = 'aprooved';
        $pendingTransaction->save();

        return $pendingTransaction;
    }

    public function rejectAPI(Request $request, PendingTransaction $pendingTransaction)
    {
        $pendingTransaction->status = 'rejected';
        $pendingTransaction->save();
        broadcast(new PendingTransactionRejected($pendingTransaction));

        return $pendingTransaction;
    }
}
