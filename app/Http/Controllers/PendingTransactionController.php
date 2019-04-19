<?php

    namespace App\Http\Controllers;

    use App\Account;
    use App\Events\PendingTransactionRejected;
    use App\Http\Requests\CreateTransaction;
    use App\PendingTransaction;
    use App\Services\CreateTransactionService;
    use App\User;
    use Doctrine\DBAL\Query\QueryBuilder;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class PendingTransactionController extends Controller
    {
        public function index()
        {
            return view('coordinator.pendingTransactions');
        }

        public function indexAPI(Request $request)
        {

            $limit = $request->has('perPage') ? $request->get('perPage') : 20;
            $q = $request->has('q') ? $request->get('q') : null;
            if ($q) {
                return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner', 'foreign_account.owner'])
                    ->whereHas('client', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('idn', 'like', '%' . $q . '%')
                            ->orWhere('idn_type', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('receiver_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('operator_account.owner', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('operator_account.bank', function ($query) use ($q) {
                        return $query->where('name', 'like', '%' . $q . '%');
                    })
                    ->orWhere('id', 'like', '%' . $q . '%')
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit);

            }
            return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner', 'foreign_account.owner'])->paginate($limit);
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
                $limit = $request->has('perPage') ? $request->get('perPage') : 20;
                $q = $request->has('q') ? $request->get('q') : null;
                $foreignUser = $request->user();
                if ($q) {
                    return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner', 'foreign_account.owner'])
                        ->whereHas('foreign_account', function ($q) use ($foreignUser) {
                            return $q->where('user_id', $foreignUser->id);
                        })
                        ->whereHas('client', function ($query) use ($q) {
                            return $query->where('name', 'like', '%' . $q . '%')
                                ->orWhere('last_name', 'like', '%' . $q . '%')
                                ->orWhere('idn', 'like', '%' . $q . '%')
                                ->orWhere('idn_type', 'like', '%' . $q . '%')
                                ->orWhere('email', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('receiver_account.bank', function ($query) use ($q) {
                            return $query->where('name', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('operator_account.owner', function ($query) use ($q) {
                            return $query->where('name', 'like', '%' . $q . '%')
                                ->orWhere('last_name', 'like', '%' . $q . '%');
                        })
                        ->orWhereHas('operator_account.bank', function ($query) use ($q) {
                            return $query->where('name', 'like', '%' . $q . '%');
                        })
                        ->orWhere('id', 'like', '%' . $q . '%')
                        ->orderBy('created_at', 'desc')
                        ->paginate($limit);

                }
                return PendingTransaction::with(['client', 'receiver_account', 'operator_account.owner', 'foreign_account.owner'])
                    ->whereHas('foreign_account', function ($q) use ($foreignUser) {
                        return $q->where('user_id', $foreignUser->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate($limit);
            }
        }

        public function approveAPI(Request $request, PendingTransaction $pendingTransaction, CreateTransactionService $createTransactionService)
        {

            $pendingTransaction->status = 'aprooved';
            $pendingTransaction->save();
            $transactionRequest = new CreateTransaction($pendingTransaction->toArray());
            $transactionRequest->setUserResolver($request->getUserResolver());
            return $createTransactionService->make($transactionRequest);
        }
        public function rejectAPI(Request $request, PendingTransaction $pendingTransaction)
        {

            $pendingTransaction->status = 'rejected';
            $pendingTransaction->save();
            broadcast(new PendingTransactionRejected($pendingTransaction));
            return $pendingTransaction;
        }
    }
