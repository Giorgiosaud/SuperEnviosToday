<?php

namespace App\Http\Controllers;

use App\Account;
use App\Attachment;
use App\Events\TransactionExecuted;
use App\Http\Requests\CreateTransaction;
use App\Role;
use App\Services\CreateTransactionService;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TransactionController extends Controller
{

    /**
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('operators.transactions');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function pending()
    {
        return view('operators.transactions-pending');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'to_account_id' => 'required|exists:accounts,id',
            'to_user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric'
        ]);
        $validData['client_id'] = $request->user()->id;
        $validData['status'] = 'terminated';
        $validData['type'] = 'income';
        broadcast(new TransactionExecuted($request->user(), 'made transaction'))->toOthers();
        return Transaction::create($validData);
    }

    public function normalstore(CreateTransaction $request, CreateTransactionService $createTransactionService)
    {
        return $createTransactionService->make($request);
    }

    public function list()
    {
        return view('operators.list');
    }

    public function myTransactionsAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 20;
        $user = $request->user();
        $accountsId = $user->accounts->pluck('id');
        return Transaction::with(['client', 'toUser', 'destinationAccount.bank.currency', 'relatedTransactions.client', 'relatedTransactions.toUser', 'relatedTransactions.destinationAccount.bank.currency', 'relatedTransactions.fromUser', 'relatedTransactions.originAccount.bank.currency'])
            ->where('related_transaction_id', null)
            ->where(function ($q) use ($accountsId) {
                return $q->whereIn('to_account_id', $accountsId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function allTransactionsAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 20;
        return Transaction::with(['client', 'toUser', 'destinationAccount.bank.currency', 'relatedTransactions.toUser', 'relatedTransactions.destinationAccount.bank.currency', 'relatedTransactions.fromUser', 'relatedTransactions.originAccount.bank.currency'])
            ->where('related_transaction_id', null)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function venezuelanTransactionsAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 20;
        if ($request->user()->hasRole('coordinator')) {
            $usersId = Role::find('venezuelan_operator')->users->pluck('id');
            $accountsIds = Account::whereHas('owners', function ($q) use ($usersId) {
                return $q->whereIn('user_id', $usersId);
            })->get()->pluck('id');
        } else {
            $accountsIds = $request->user()->accounts->pluck('id');
        }
        $transactions = Transaction::with(['destinationAccount', 'parentTransaction.toUser', 'originAccount', 'fromUser', 'toUser'])->whereIn('from_account_id', $accountsIds)->paginate($limit);

        return $transactions;
    }

    public function venezuelanTransactionsCountAPI(Request $request)
    {
        if ($request->user()->hasRole('coordinator')) {
            $usersId = Role::find('venezuelan_operator')->users->pluck('id');
            $accountsIds = Account::whereHas('owners', function ($q) use ($usersId) {
                return $q->whereIn('user_id', $usersId);
            })->get()->pluck('id');
        } else {
            $accountsIds = $request->user()->accounts->pluck('id');
        }
        return Transaction::with(['destinationAccount.fromUser', 'parentTransaction.destinationAccount.fromUser', 'originAccount'])->whereIn('from_account_id', $accountsIds)->where('status', '!=', 'executed')->count();
    }

    public function venezuelanTransactionsConfirmationAPI(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'attachments.*' => 'required|numeric',
            'transactionNumber' => 'required|numeric'
        ]);
        foreach ($validated['attachments'] as $attachment) {
            $a = Attachment::find($attachment);
            $transaction->attachments()->save($a);
        }
        $transaction['transaction_number'] = $validated['transactionNumber'];
        $transaction['status'] = 'executed';
        $transaction->save();
        return $transaction;
    }

    public function venezuelanTransactionsInProgressAPI(Transaction $transaction)
    {
        $transaction['status'] = 'in_progress';
        $transaction->save();
        return $transaction;
    }

    public function addTransaction()
    {
        return view('coordinator.addTransactions');
    }

    public function listTransactions()
    {
        return view('coordinator.listTransactions');
    }

    public function finishTransaction(Transaction $transaction)
    {
        $related = $transaction->relatedTransactions;
        foreach ($related as $tr) {
            $tr->status = 'terminated';
            $tr->save();
        }
        $transaction->status = 'terminated';
        $transaction->save();
        return $transaction;
    }

    public function fixTransaction()
    {
        return view('coordinator.fixTransactions');
    }

    public function adjustTransaction(Request $request)
    {
        $validated = $request->validate([
            "to_account_id" => 'required|exists:accounts,id',
            'to_user_id' => 'required|exists:users,id',
            "type" => 'required|string|in:income,outcome',
            "amount" => 'required|numeric'
        ]);
        $validated['status'] = 'terminated';
        return Transaction::create($validated);
    }
    public function isRepeated(Request $request)
    {
        $transaction1 = Transaction::whereDate('created_at', Carbon::today())->where(['from_user_id' => $request->client_id, 'amount' => $request->amount * 10000])->first();
        if (!$transaction1) {
            return ['isRepeated' => false];
        }
        $transaction2 = Transaction::whereDate('created_at', Carbon::today())->where(['to_account_id' => $request->receiver_account_id, 'related_transaction_id' => $transaction1->id, 'amount' => $request->rate * $request->amount * 10000])->first();

        if (!$transaction2) {
            return ['isRepeated' => false];
        }
        return ['isRepeated' => true];
    }
}
