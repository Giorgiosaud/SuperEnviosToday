<?php

    namespace App\Http\Controllers;

    use App\Account;
    use App\Attachment;
    use App\Events\PendingTransactionAwaiting;
    use App\Events\TransactionExecuted;
    use App\Http\Requests\CreateTransaction;
    use App\PendingTransaction;
    use App\Rate;
    use App\Services\CreateTransactionService;
    use App\Setting;
    use App\Transaction;
    use Carbon\Carbon;
    use Illuminate\Http\Request;

    class TransactionController extends Controller
    {

        /**
         *
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            return view('operators.transactions');
            //
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function pending()
        {
            return view('operators.transactions-pending');
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\Response
         */
        public function store(Request $request)
        {
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
            $mainTransactions = Transaction::where(function ($q) use ($accountsId) {
                return $q->where('related_transaction_id', null)
                    ->whereIn('to_account_id', $accountsId);
            })->get();
            $mainTransactionsIds = $mainTransactions->pluck('id');
            return Transaction::with(['client','destinationAccount.owner','destinationAccount.bank.currency'])
                ->where(function ($q) use ($accountsId) {
                    return $q->where('related_transaction_id', null)
                        ->whereIn('to_account_id', $accountsId);
                })->orWhereIn('related_transaction_id', $mainTransactionsIds)
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        }

    }
