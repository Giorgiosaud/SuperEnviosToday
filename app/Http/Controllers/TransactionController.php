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

  class TransactionController extends Controller {

    /**
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
      return view('operators.transactions');
      //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function pending() {
      return view('operators.transactions-pending');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request) {
      $validData = $request->validate([
        'to_account_id' => 'required|numeric',
        'amount' => 'required|numeric'
      ]);
      $validData['user_id'] = $request->user()->id;
      $validData['status'] = 'terminated';
      $validData['type'] = 'income';
      broadcast(new TransactionExecuted($request->user(), 'made transaction'))->toOthers();
      return Transaction::create($validData);
    }

    public function normalstore(CreateTransaction $request, CreateTransactionService $createTransactionService) {
      return $createTransactionService->make($request);
    }

    public function list() {
      return view('operators.list');

    }

    public function myTransactionsAPI(Request $request) {
      $limit = $request->has('perPage') ? $request->get('perPage') : 20;

      $user = $request->user();
      $accountsId = $user->accounts->pluck('id');
      return Transaction::with(['client', 'destinationAccount.owner', 'destinationAccount.bank.currency', 'relatedTransactions.client', 'relatedTransactions.destinationAccount.owner', 'relatedTransactions.destinationAccount.bank.currency', 'relatedTransactions.originAccount.owner', 'relatedTransactions.originAccount.bank.currency'])
        ->where('related_transaction_id', null)
        ->where(function ($q) use ($accountsId) {
          return $q->whereIn('to_account_id', $accountsId);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($limit);
    }

    public function venezuelanTransactionsAPI(Request $request) {
      $limit = $request->has('perPage') ? $request->get('perPage') : 20;
      if ($request->user()->hasRole('coordinator')) {
        $usersId = Role::find('venezuelan_operator')->users->pluck('id');
        $accountsId = Account::whereIn('user_id', $usersId)->get();
      } else {
        $accountsId = $request->user()->accounts->pluck('id');
      }
      $transactions = Transaction::with(['destinationAccount.owner', 'relatedTransaction.destinationAccount.owner', 'originAccount'])->whereIn('from_account_id', $accountsId)->paginate($limit);

      return $transactions;
    }

    public function venezuelanTransactionsCountAPI(Request $request) {
      if ($request->user()->hasRole('coordinator')) {
        $usersId = Role::find('venezuelan_operator')->users->pluck('id');
        $accountsId = Account::whereIn('user_id', $usersId)->get();
      } else {
        $accountsId = $request->user()->accounts->pluck('id');
      }
      return Transaction::with(['destinationAccount.owner', 'relatedTransaction.destinationAccount.owner', 'originAccount'])->whereIn('from_account_id', $accountsId)->where('status', '!=', 'executed')->count();

    }

    public function venezuelanTransactionsConfirmationAPI(Request $request, Transaction $transaction) {
      $validated = $request->validate([
        'attachments.*' => 'required|numeric',
        'transactionNumber' => 'required|numeric']);
      foreach ($validated['attachments'] as $attachment) {
        $a = Attachment::find($attachment);
        $transaction->attachments()->save($a);
      }
      $transaction['transaction_number'] = $validated['transactionNumber'];
      $transaction['status'] = 'executed';
      $transaction->save();
      return $transaction;
    }

    public function venezuelanTransactionsInProgressAPI(Transaction $transaction) {
      $transaction['status'] = 'in_progress';
      $transaction->save();
      return $transaction;
    }

    public function addTransaction() {
      return view('coordinator.addTransactions');

    }

    public function listTransactions() {
      return view('coordinator.listTransactions');
    }

    public function finishTransaction(Transaction $transaction) {
      $transaction->status = 'terminated';
      $transaction->save();
      return $transaction;
    }
  }
