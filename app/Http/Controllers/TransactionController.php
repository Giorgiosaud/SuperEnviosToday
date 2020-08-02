<?php

namespace App\Http\Controllers;

use App\Account;
use App\Attachment;
use App\Events\TransactionExecuted;
use App\Http\Requests\CreateTransaction;
use App\Role;
use App\Services\CreateTransactionService;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('operators.transactions');
        //
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pending()
    {
        return view('operators.transactions-pending');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'to_account_id' => 'required|exists:accounts,id',
            'to_user_id'    => 'required|exists:users,id',
            'amount'        => 'required|numeric',
        ]);
        $validData['client_id'] = $request->user()->id;
        $validData['status'] = 'terminated';
        $validData['type'] = 'income';
        broadcast(new TransactionExecuted($request->user(), 'made transaction'))->toOthers();
        return Transaction::create($validData);
    }

    /**
     * @param CreateTransaction $request
     * @param CreateTransactionService $createTransactionService
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response|void
     */
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
        $limit = $request->has('perPage') ? $request->get('perPage') : 10;
        $user = $request->user();
        $accountsId = $user->accounts->pluck('id');
        $date=$request->date;
        if($date){
          $date=Carbon::parse($date)->format('Y-m-d');
          $nextDate=Carbon::parse($date)->addDay()->format('Y-m-d');
          return Transaction::with(['client', 'toUser', 'destinationAccount.bank', 'relatedTransactions.client', 'relatedTransactions.toUser', 'relatedTransactions.destinationAccount.bank', 'relatedTransactions.fromUser', 'relatedTransactions.originAccount.bank'])
            ->where('related_transaction_id', null)
            ->where('created_at','>=',$date)
            ->where('created_at','<',$nextDate)
            ->where(function ($q) use ($accountsId) {
                return $q->whereIn('to_account_id', $accountsId);
            })
            ->orderByRaw('FIELD(status, "assigned","in_progress","executed","confirmed","terminated")')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
        }
        return Transaction::with(['client', 'toUser', 'destinationAccount.bank', 'relatedTransactions.client', 'relatedTransactions.toUser', 'relatedTransactions.destinationAccount.bank', 'relatedTransactions.fromUser', 'relatedTransactions.originAccount.bank'])
            ->where('related_transaction_id', null)
            ->where('to_account_id','<>',null)
            ->whereIn('to_account_id', $accountsId)
            ->orderByRaw('FIELD(status, "assigned","in_progress","executed","confirmed","terminated")')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function allTransactionsAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 10;

        return Transaction::with(['client', 'toUser', 'destinationAccount.bank.currency', 'relatedTransactions.toUser', 'relatedTransactions.destinationAccount.bank.currency', 'relatedTransactions.fromUser', 'relatedTransactions.originAccount.bank.currency'])
            ->where('related_transaction_id', null)
            ->orderByRaw('FIELD(status, "assigned","in_progress","executed","confirmed","terminated")')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    public function venezuelanTransactionsAPI(Request $request)
    {
        $limit = $request->has('perPage') ? $request->get('perPage') : 10;
        $accountsIds = $this->getAccountsIds($request);
        $transactions = Transaction::with(['destinationAccount', 'parentTransaction.toUser', 'originAccount', 'fromUser', 'toUser'])
        ->whereIn('from_account_id', $accountsIds)
        ->orderByRaw('FIELD(status, "assigned","in_progress","executed","confirmed","terminated")')
        ->orderBy('created_at', 'desc')
        ->paginate($limit);

        return $transactions;
    }

    public function venezuelanTransactionsCountAPI(Request $request)
    {
        $accountsIds = $this->getAccountsIds($request);

        return Transaction::whereIn('from_account_id', $accountsIds)->where('status', 'assigned')->orWhere('status', 'in_progress')->count();
    }

    public function venezuelanTransactionsConfirmationAPI(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'attachments.*'     => 'required|numeric',
            'transactionNumber' => 'required|numeric',
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
            'to_account_id' => 'required|exists:accounts,id',
            'to_user_id'    => 'required|exists:users,id',
            'type'          => 'required|string|in:income,outcome',
            'amount'        => 'required|numeric',
        ]);
        $validated['status'] = 'terminated';
        $validated['from_account_id'] = $validated['to_account_id'];

        return Transaction::create($validated);
    }

    public function isRepeated(Request $request)
    {
        $transactionSameNumber = Transaction::where(['transaction_number' => $request->transaction_number])->first();
        if ($transactionSameNumber) {
            return ['isRepeated' => true];
        }
        $transactionTodayAndSameAmountAndUser = Transaction::whereDate('created_at', Carbon::today())->where(['from_user_id' => $request->client_id, 'amount' => $request->amount * 10000])->first();
        if (!$transactionTodayAndSameAmountAndUser) {
            return ['isRepeated' => false];
        }
        $transactionWithamountTransactionIdReceiverAccount = Transaction::whereDate('created_at', Carbon::today())->where([
            'to_account_id'          => $request->receiver_account_id,
            'related_transaction_id' => $transactionTodayAndSameAmountAndUser->id,
            'amount'                 => $request->rate * $request->amount * 10000,
            ])->first();

        if (!$transactionWithamountTransactionIdReceiverAccount) {
            return ['isRepeated' => false];
        }

        return ['isRepeated' => true];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    protected function getAccountsIds(Request $request): \Illuminate\Support\Collection
    {
        if ($request->user()->hasRole('coordinator')) {
            $usersId = Role::find('venezuelan_operator')->users->pluck('id');
            $accountsIds = Account::whereHas('owners', function ($q) use ($usersId) {
                return $q->whereIn('user_id', $usersId);
            })->where('is_operator_account', true)->get()->pluck('id');
        } else {
            $accountsIds = $request->user()->accounts->pluck('id');
        }
        return $accountsIds;
    }
}
